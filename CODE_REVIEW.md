# 📋 Code Review - TimestampBundle

**Date :** 7 février 2026  
**Repos :**
- [timestamp-bundle](https://github.com/stephane-fillion/timestamp-bundle)
- [test-technique-backend](https://github.com/stephane-fillion/test-technique-backend)

---

## 📊 Résumé des tests

### Avant corrections (code original)

```
Tests: 10, Assertions: 33, Failures: 3

✅ Le bundle TimestampBundle est enregistré
✅ Le bundle est dans le dossier vendor
✅ Le trait TimestampableTrait existe
✅ L'EventSubscriber existe
❌ createdAt est automatiquement rempli lors de la création
❌ updatedAt est automatiquement rempli lors de la modification
❌ Le timestamp fonctionne aussi sur Comment
✅ Category n'est pas affectée
✅ createdAt n'est pas modifié lors d'un update
✅ Les méthodes getCreatedAt et setCreatedAt existent
```

**Score original : 7/10 tests passés**

### Après corrections

```
Tests: 10, Assertions: 20, Failures: 0

✅ Le bundle TimestampBundle est enregistré
✅ Le bundle est dans le dossier vendor
✅ Le trait TimestampableTrait existe
✅ L'EventSubscriber existe
✅ createdAt est automatiquement rempli lors de la création
✅ updatedAt est automatiquement rempli lors de la modification
✅ Le timestamp fonctionne aussi sur Comment
✅ Category n'est pas affectée
✅ createdAt n'est pas modifié lors d'un update
✅ Les méthodes getCreatedAt et setCreatedAt existent
```

**Score après corrections : 10/10**

---

## 🐛 Problèmes identifiés

### 1. ❌ CRITIQUE : `loadExtension()` et `services.yaml` supprimés

**Historique des commits :**

| Commit | Message |
|--------|---------|
| `8423973` | Création TimestampBundle |
| `60c8dba` | Correction namespace + nommage correct |
| `f27fbc1` | Correction configuration |
| `3e9f27a` | Chargement de la configuration |
| `e618e58` | Correction configuration |
| `6d48319` | ❌ Utilisation attribut (supprime loadExtension + services.yaml) |

Au commit `6d48319`, `loadExtension()` et `config/services.yaml` ont été supprimés, pensant que l'attribut `#[AsDoctrineListener]` suffisait.

**Problème :** L'attribut `#[AsDoctrineListener]` ne fonctionne que si la classe est **déjà un service Symfony**. Sans `services.yaml`, le subscriber n'est jamais instancié.

**Fichier actuel :** [TimestampBundle.php](timestamp-bundle/src/TimestampBundle.php#L12-L14)

```php
class TimestampBundle extends AbstractBundle
{
    // VIDE - loadExtension() supprimé
}
```

**Correction appliquée :**

```php
class TimestampBundle extends AbstractBundle
{
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/services.yaml');
    }
}
```

---

### 2. ⚠️ ERREUR : Mauvais type Doctrine pour les colonnes

**Fichier :** [TimestampableTrait.php](timestamp-bundle/src/Traits/TimestampableTrait.php#L13-L17)

```php
#[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]  // ❌
```

**Problème :** `DATE_IMMUTABLE` stocke uniquement la **date** (ex: `2026-02-07`), pas l'heure. Pour un timestamp, il faut `DATETIME_IMMUTABLE` qui stocke date + heure (ex: `2026-02-07 14:30:00`).

**Correction appliquée :**

```php
#[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]  // ✅
```

---

### 3. ℹ️ Note : `#[Timestampable]` sur le trait

Au commit `3e9f27a`, l'attribut `#[Timestampable]` était placé sur le **trait** :

```php
#[Timestampable]
trait TimestampableTrait
```

**Problème :** `ReflectionClass::getAttributes()` ne remonte pas les attributs des traits utilisés. L'attribut doit être sur la **classe** qui utilise le trait.

**Solution correcte (dans test-technique-backend) :**

```php
#[Timestampable]
class Article
{
    use TimestampableTrait;
}
```

C'est ce qui a été fait côté `test-technique-backend`, donc ça fonctionne.

---

## ✅ Ce qui était correct

- Structure du bundle (dossiers, namespaces)
- L'attribut `#[Timestampable]`
- Le trait `TimestampableTrait` (logique correcte)
- L'EventSubscriber `TimestampableSubscriber` (logique correcte)
- Intégration dans `test-technique-backend` (composer.json, bundles.php, entités)

---

## 📁 Structure du bundle

```
timestamp-bundle/
├── composer.json              ✅ Correct
├── config/
│   └── services.yaml          ✅ Créé (était supprimé)
├── src/
│   ├── TimestampBundle.php    ✅ Corrigé (loadExtension ajouté)
│   ├── Attribute/
│   │   └── Timestampable.php  ✅ Correct
│   ├── EventSubscriber/
│   │   └── TimestampableSubscriber.php  ✅ Correct
│   └── Traits/
│       └── TimestampableTrait.php       ✅ Corrigé (DATETIME)
```

---

## 🔧 Corrections appliquées

### 1. [TimestampBundle.php](timestamp-bundle/src/TimestampBundle.php)

Ajout de `loadExtension()` :

```php
public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
{
    $container->import('../config/services.yaml');
}
```

### 2. [TimestampableTrait.php](timestamp-bundle/src/Traits/TimestampableTrait.php)

Correction du type Doctrine :

```php
#[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
private ?\DateTimeImmutable $createdAt = null;

#[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
private ?\DateTimeImmutable $updatedAt = null;
```

### 3. [config/services.yaml](timestamp-bundle/config/services.yaml)

Fichier recréé :

```yaml
services:
    _defaults:
        autowire: true
        autoconfigure: true

    TimestampBundle\EventSubscriber\TimestampableSubscriber: ~
```

---

## 📊 Résultat

| Avant | Après |
|-------|-------|
| 7/10 tests | 10/10 tests |
| createdAt = NULL | createdAt = DateTimeImmutable ✅ |
| updatedAt = NULL | updatedAt = DateTimeImmutable ✅ |
