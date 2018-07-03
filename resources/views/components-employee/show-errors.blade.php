    <div id="alert" class='allerta' roles='alert'>
        <ul>
        @foreach($errors->all() as $error)
            <li>
                {{$error}}
            </li>
        @endforeach
        </ul>
    </div>
