<script>
    !function(e,o,n){window.HSCW=o,window.HS=n,n.beacon=n.beacon||{};var t=n.beacon;t.userConfig={},t.readyQueue=[],t.config=function(e){this.userConfig=e},t.ready=function(e){this.readyQueue.push(e)},o.config={docs:{enabled:!1,baseUrl:""},contact:{enabled:!0,formId:"{{ Config::get('services.helpscout.form_id') }}"}};var r=e.getElementsByTagName("script")[0],c=e.createElement("script");c.type="text/javascript",c.async=!0,c.src="https://djtflbt20bdde.cloudfront.net/",r.parentNode.insertBefore(c,r)}(document,window.HSCW||{},window.HS||{});

    // http://developer.helpscout.net/beacons/
    HS.beacon.config({
        poweredBy: false,
        color: '#2980B9',
        icon: 'message',
        instructions: "{{ trans('contact.helpscoutInfo') }}"
    });
</script>

{{-- Current user details if available --}}
@if (!is_null($user))
    <script>
        // http://developer.helpscout.net/beacons/
        HS.beacon.ready(function() {
          HS.beacon.identify({
            name: '{{ $user->login }}',
            email: '{{ $user->email }}',
            id: {{ $user->id }}
        });
      });
    </script>
@endif
