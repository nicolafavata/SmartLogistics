<!DOCTYPE html>
<html lang="en" class="no-js">
	<head>
		<meta charset="UTF-8" />
        <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta name="description" content="Applicazione web per la logistica intelligente nelle Smart City">
		<meta name="author" content="Nicola Favata">
		<meta name="csrf-token" content="{{ csrf_token() }}">
		<script>
            window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
		</script>

		<title>@yield('title','Home')</title>

		<link rel="shortcut icon" href="../favicon.ico">
		<link href="css/mystyle.css" rel="stylesheet">


		<link rel="stylesheet" type="text/css" href="css/profile/normalize.css" />
		<link rel="stylesheet" type="text/css" href="css/profile/profile.css" />
		<link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.2.0/css/font-awesome.min.css" />
		<link rel="stylesheet" type="text/css" href="css/profile/menu_topside.css" />
		<!--[if IE]>
  		<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
	</head>
	<body onload="hideloader()">
		<div class="container">
			<div class="menu-wrap">
				<nav class="menu-top">
					<div class="profile"><img class="border-border-verde"
											@if ($dati->img_employee=='0')
											  src="img/profile.jpg"
											  @else
													  src="{{env('APP_URL').'/storage/'.$dati->img_employee}}"
													  @endif
											  width="50" height="50" alt="{{$dati->name.' '.$dati->cognome}}"/><span>{{$dati->name.' '.$dati->cognome}}</span></div>
					<div class="icon-list">
						<a href="{{ route('employee') }}" title="La mia home"><i class="fa fa-home"></i></a>
						<a id="close-button" title="Chiudi menù"><i class="fa fa-times-circle"></i></a>
						<a href="{{ route('my_profile') }}" title="Il mio profilo"><i class="fa fa-user"></i></a>
						<a href="{{ route('picture') }}" title="Foto profilo"><i class="fa fa-camera"></i></a>
						<a href="{{ route('new_password') }}" title="Resetta la password"><i class="fa fa-key"></i></a>
						<a href="{{ route('logout') }}"
								onclick="event.preventDefault();
						   document.getElementById('logout-form').submit();" title="Esci"><i class="fa fa-sign-out"></i></a>
						<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
							{{ csrf_field() }}
						</form>
					</div>
				</nav>
				<nav class="menu-side">
					<ul id="menu_a_2livelli">
						@if($dati->responsabile=='1')
						<li>
							<span>Sede dell'azienda</span>
							<ul>
								<li><a href="{{ route('my_company') }}">Dati aziendali</a></li>
								<li><a href="{{ route('upcompany') }}">Modifica dati</a></li>
								<li><a href="{{ route('visiblecompany') }}">Visibilità</a></li>
							</ul>
						</li>
						<li>
							<span>Dipendenti</span>
							<ul>
								<li><a href="{{ route('viewemployees') }}">Visualizza</a></li>
								<li><a href="{{ route('addemployee') }}">Aggiungi</a></li>
								<li><a href="{{ route('upemployee') }}">Modifica</a></li>
								<li><a href="{{ route('delemployee') }}">Elimina</a></li>
							</ul>
						</li>
						<li>
							<span>Supply chain</span>
							<ul>
								<li><a href="{{ route('supplyresearch') }}">Ricerca azienda</a></li>
								<li><a href="{{ route('supplychainmanagement')}}">Gestione aggregazioni</a></li>
								<li><a href="{{route('requests-received')}}">Richieste ricevute</a></li>
								<li><a href="{{route('requests-transmitted')}}">Richieste trasmesse</a></li>
								<li><a href="{{route('block-supply')}}">Aziende bloccate</a></li>
							</ul>
						</li>
						@endif
						@if($dati->acquisti=='1')
						<li>
							<span>Acquisti</span>
							<ul>
								<li><a href="{{route('inventories')}}">Inventario</a></li>
								<li><a href="{{route('providers')}}">Fornitori</a></li>
								<li><a href="{{route('expires')}}">Scadenze</a></li>
								<li><a href="{{route('providers')}}">Genera ordine</a></li>
								<li><a href="#">Ordini effettuati</a></li>
							</ul>
						</li>
							@endif
							@if($dati->produzione=='1')
						<li>
							<span>Produzione</span>
							<ul>
								<li><a href="{{route('production')}}">Catalogo</a></li>
								<li><a href="{{route('mapping-production')}}">Associazione acquisti produzione</a></li>
							</ul>
						</li>
							@endif
							@if($dati->vendite=='1')
						<li>
							<span>Vendite</span>
							<ul>
								<li><a href="{{route('catalogue')}}">Catalogo</a></li>
								<li><a href="{{route('add_catalogue')}}">Listini</a></li>
								<li><a href="{{route('expire-monitor')}}">Monitoraggio delle scadenze</a></li>
								<li><a href="{{route('list-invoice')}}">Fatture</a></li>
								<li><a href="{{route('list-sales-order')}}">Ordini</a></li>
								<li><a href="{{route('desk-sales-list')}}">Vendite al banco</a></li>
							</ul>
						</li>
								<li>
									<span>Nuovo documento</span>
									<ul>
										<li><a href="{{route('new-sales-invoice')}}">Fattura di vendita</a></li>
										<li><a href="{{route('new-sales-order')}}">Ordine cliente</a></li>
										<li><a href="{{route('new-sales-desk')}}">Vendita al banco</a></li>
									</ul>
								</li>
							@endif
						<li>
							<a class="dropdown-item" href="{{ route('logout') }}"
							   onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
								<i class="fa fa-sign-out"></i>&nbsp;Esci
							</a>
						</li>
					</ul>
				</nav>
			</div>


			<button class="menu-button" title="Start" id="open-button">Start</button>
			<div class="content-wrap">
				<div class="content">
					<header class="codrops-header">
						<img class="img-fluid image-responsive" src="img/logo.gif"   alt="logo_smartlogis">
						@yield('content_header')
					</header>
						@if(session()->has('message'))
							@component('components-employee.alert-success')
								{{session()->get('message')}}
							@endcomponent
						@endif
						@if(count($errors))
							@component('components-employee.show-errors')
								{{$errors}}
							@endcomponent
						@endif
						<!-- Dinamic content -->
						@yield('content_section')


					@section('footer')
						<!-- spnner -->
							<div id="loading">
							</div>

						<!-- footer -->
						<div class="container-fluid footer-employee">
							<div class="row">
								<div class="col-sm-12">
									<br />
									<h3 class="card-text">Università degli studi "Guglielmo Marconi"</h3>
										<div class="card-body">
											<h4 class="card-title">Facoltà di Scienze e Tecnologie Applicate</br>Corso di Laurea in Ingegneria Informatica</h4>
												<h3 class="card-title">Sviluppo di un'applicazione web per la logistica nelle Smart City</h3>
												<p class="card-text">Candidato: Nicola Favata - Matricola: 008272 - Relatore: Luca Regoli</p>
										</div>
										<div class="card-body">
											Anno accademico 2017/2018
										</div>
								</div>
							</div>
						</div>
				</div><!-- /content -->
			</div><!-- /content-wrap -->
		</div><!-- /container -->

		@section('script')
		<script src="js/jquery-3.3.1.slim.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
		<script src="js/bootstrap.js"></script>
		<script src="js/app.js"></script>
		<script src="js/myjs.js"></script>
		<script>
            $(document).ready(function () {
                $('div.allerta').fadeOut(45000);
            });
		</script>
		<script>
            $(document).ready(function () {
                $('div.successo').fadeOut(15000);
            });
		</script>

	</body>
</html>