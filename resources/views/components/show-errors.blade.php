    <div id="alert" class='alert alert-danger alert-dismissible' roles='alert'>
        <ul>
        @foreach($errors->all() as $error)
            <li>
                {{$error}}
            </li>
        @endforeach
        </ul>
    </div>
