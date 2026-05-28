<x-mail::message>
# Nuovo Lead Configurato

Ciao, hai ricevuto una nuova configurazione flotta da un cliente.

## Dati Azienda
**Azienda:** {{ $client['company'] }}  
**Referente:** {{ $client['contact'] }}  
**Email:** {{ $client['email'] }}  
**Telefono:** {{ $client['phone'] ?? 'Non fornito' }}  
**Traffico 4G:** {{ ($client['italia'] ?? false) ? 'Italia' : '' }} {{ ($client['estero'] ?? false) ? 'Estero' : '' }}

**Note:**  
{{ $client['notes'] ?? 'Nessuna nota aggiuntiva.' }}

## Mezzi Selezionati
<x-mail::table>
| Mezzo | Quantità |
| :--- | :--- |
@foreach($vehicles as $item)
| {{ $item['name'] }} | **{{ $item['qty'] }}** |
@endforeach
</x-mail::table>

Grazie,<br>
{{ config('app.name') }}
</x-mail::message>
