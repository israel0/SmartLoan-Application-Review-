@component('mail::message')
    <b>Priority:</b> {!! $request->priority !!} <br>
    <b>Subject:</b> {!! $request->subject !!} <br>
    <b>Department:</b> {!! $request->department !!} <br>
    <b>Sender's Email:</b> {!! $senderEmail !!} <br>
    <b>Message:</b> {!! $request->comment !!}
@endcomponent
