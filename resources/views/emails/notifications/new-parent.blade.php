@component('mail::message')
# Nouveau Parent Inscrit

Bonjour,

Un nouveau parent a été inscrit dans le système **Ancre des Élites**.

## Détails du Parent

**Nom:** {{ $parent->user->name }}  
**Email:** {{ $parent->user->email }}  
**Téléphone:** {{ $parent->telephone ?? 'Non fourni' }}  
**Date d'inscription:** {{ $parent->created_at->format('d/m/Y à H:i') }}

## Actions Disponibles

Veuillez vérifier le profil du parent et compléter la vérification de compte si nécessaire.

@component('mail::button', ['url' => route('parents.show', $parent)])
Voir le Profil
@endcomponent

Cordialement,  
**L'Équipe Ancre des Élites**

---

*Ce message a été généré automatiquement. Veuillez ne pas répondre à cet email.*
@endcomponent
