@extends('layouts.sub_admin')
@section('title','Smartlogis per le aziende')
@section('content')
    <nav class="navbar navbar-light justify-content-between barraverde">
        <div class="container-fluid text-left">
            <div class="row">
                <div class="col-md-12 text-left barraverde">
                    <h6> {{ Auth::user()->name }}&nbsp{{ Auth::user()->cognome }}&nbsp{{'queste sono le sedi aziendali'}}</h6>
                </div>
            </div>
        </div>
    </nav>
    <div class="carousel-inner-maxi admin_viewcompany">
        <div class="container admin_home">
            <div class="row">
                <div class="col-md-12 jumbotron border">
                    <div class="row">
                        @if(session()->has('message'))
                            @component('components.alert-info')
                                {{session()->get('message')}}
                            @endcomponent
                        @endif
                        @if(count($errors))
                            @component('components.show-errors')
                                {{$errors}}
                            @endcomponent
                        @endif
                            <table class="table table-borderless">
                            @forelse($company as $sede)
                                <tr>
                                    <td>
                                        <table class="table table-striped">
                                            <tr>
                                                <td>
                                                    <div class="row form-check">
                                                        <h5 class="text-uppercase font-weight-bold">
                                                            {{$sede->rag_soc_company}}
                                                        </h5>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="row form-check">
                                                        <h6 class="text-capitalize text-left">{{$sede->indirizzo_company.', '.$sede->civico_company.' - '}}
                                                        @if(($sede->cap_company)=='8092')
                                                            {{$sede->cap_company_office_extra.' '.$sede->city_company_office_extra.' '.$sede->state_company_office_extra.' - '.$sede->nazione_company}}                                                 @else
                                                        @endif
                                                            @if($sede->cap_company!='8092')
                                                            {{$sede->comune.' in provincia di '.$sede->provincia}}
                                                                @endif
                                                        </h6>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="row form-check">
                                                            <h6 class="font-weight-bold">Responsabile</h6>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <table>
                                                        <tr>
                                                            <td rowspan="2" class="barrabianca">
                                                                <div class="row form-check">
                                                                    <img class="border-border-fucsia" width="75" height="75" title="Responsabile sede" alt="Responsabile"
                                                                         @if(($sede->img_employee)!=='0'))
                                                                         src="{{'../storage/'.$sede->img_employee}}">
                                                                    @else
                                                                        src="{{'../img/profile.jpg'}}">
                                                                    @endif
                                                                </div>
                                                            </td>
                                                            <td class="barrabianca">
                                                                <div class="row form-check text-capitalize">
                                                                    {{$sede->name.' '.$sede->cognome}}
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="barrabianca">
                                                                @if($sede->tel_employee!=null)
                                                                    <div class="row form-check text-capitalize">
                                                                        {{'Telefono sede: '.$sede->tel_employee}}
                                                                    </div>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>

                                                        <div class="row form-check">
                                                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal{{$sede->id_company_office}}">
                                                                Elimina sede
                                                            </button>
                                                            <!-- Modal -->
                                                            <div class="modal fade hide" id="exampleModal{{$sede->id_company_office}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                                <div class="modal-dialog" role="document">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title text-uppercase" id="exampleModalLabel">Elimina sede aziendale</h5>
                                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                <span aria-hidden="true">&times;</span>
                                                                            </button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <h5 class="fucsia font-weight-bold">Attenzione!!!</h5>
                                                                            <p>Tutti i dati della sede verranno eliminati definitivamente (l'inventario di magazzino, le previsioni sulle vendite, etc). Gli account degli impiegati saranno cancellati dai nostri archivi e non potranno più accedere in piattaforma.</p>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                                                                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#deleteModal{{$sede->id_company_office}}">Sei sicuro?</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <!--Modal delete -->
                                                            <div class="modal fade bd-example-modal-sm hide" id="deleteModal{{$sede->id_company_office}}" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                                <div class="modal-dialog modal-sm">
                                                                    <div class="modal-content form-check">
                                                                        <form onsubmit="showloader()" method="POST" action="{{ route('deletecompany', $sede->id_company_office) }}">
                                                                        {{ csrf_field() }}
                                                                            {{'Premi conferma per proseguire con la cancellazione'}}
                                                                            <div class="modal-footer">
                                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                                                                                <button type="submit" class="btn btn-primary" data-toggle="modal" data-target="#deleteModal{{$sede->id_company_office}}">Conferma</button>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                         </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            @empty
                                    <tr>
                                        <td>
                                            <h4 class="fucsia">Non sono state inserite sedi aziendali</h4>
                                        </td>
                                    </tr>
                            @endforelse
                                <tr>
                                    <td>
                                        <div class="row">
                                            <div class="col-md-8 push-2">
                                                {{$company->links('vendor.pagination.bootstrap-4')}}
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection
@section('footer')
    @parent

@endsection
@section('script')
    @parent

@endsection