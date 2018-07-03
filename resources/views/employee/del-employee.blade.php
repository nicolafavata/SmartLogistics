@extends('layouts.employees')
@section('title','Smartlogis per le aziende')
@section('content')
    <div class="carousel-inner crudemployee_img">
        <br /><br /><br/><br />
        <div class="container admin_home">
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
                <div class="col-md-12 jumbotron border">
                    <div class="row">
                        <table class="table table-borderless table-responsive">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>
                                        Matricola
                                    </th>
                                    <th>
                                        Nome Cognome
                                    </th>
                                    <th>
                                        Email
                                    </th>
                                    <th>
                                        Telefono
                                    </th>
                                    <th>
                                        Cellulare
                                    </th>
                                    <th>

                                    </th>
                                </tr>
                            </thead>
                         @forelse($employee as $employees)
                             <tr>
                                 <td>
                                     <div class="profile"><img class=" text-right"
                                                               @if ($employees->img_employee==null)
                                                                src="img/profile.jpg"
                                                               @else
                                                                src="{{'../storage/'.$employees->img_employee}}"
                                                               @endif
                                                                width="50" height="50" alt="{{$dati->name.' '.$dati->cognome}}"/></div>
                                 </td>
                                 <td class="text-uppercase">
                                     {{$employees->matricola}}
                                 </td>
                                 <td class="capitalize">
                                     {{$employees->name.' '.$employees->cognome}}
                                 </td>
                                 <td class="text-lowercase">
                                     {{$employees->email}}
                                 </td>
                                 <td class="text-uppercase">
                                     {{$employees->tel_employee}}
                                 </td>
                                 <td class="text-uppercase">
                                     {{$employees->cell_employee}}
                                 </td>
                                 <td>
                                     <div class="row form-check">
                                         <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal{{$employees->user_employee}}">
                                             Elimina dipendente
                                         </button>
                                         <!-- Modal -->
                                         <div class="modal fade hide" id="exampleModal{{$employees->user_employee}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                             <div class="modal-dialog" role="document">
                                                 <div class="modal-content">
                                                     <div class="modal-header">
                                                         <h5 class="modal-title text-uppercase" id="exampleModalLabel">Elimina dipendente</h5>
                                                         <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                             <span aria-hidden="true">&times;</span>
                                                         </button>
                                                     </div>
                                                     <div class="modal-body">
                                                         <h5 class="fucsia font-weight-bold">Attenzione!!!</h5>
                                                         <p>L'account del dipendente sarà definitivamente eliminato e non potrà più accedere in piattaforma.</p>
                                                     </div>
                                                     <div class="modal-footer">
                                                         <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                                                         <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#deleteModal{{$employees->user_employee}}">Sei sicuro?</button>
                                                     </div>
                                                 </div>
                                             </div>
                                         </div>
                                         <!--Modal delete -->
                                         <div class="modal fade bd-example-modal-sm hide" id="deleteModal{{$employees->user_employee}}" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                             <div class="modal-dialog modal-sm">
                                                 <div class="modal-content form-check">
                                                     <form onsubmit="showloader()" method="POST" action="{{ route('delete-employee', $employees->user_employee) }}">
                                                         {{ csrf_field() }}
                                                         {{'Premi conferma per proseguire con la cancellazione'}}
                                                         <div class="modal-footer">
                                                             <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                                                             <button type="submit" class="btn btn-primary" data-toggle="modal" data-target="#deleteModal{{$employees->user_employee}}">Conferma</button>
                                                         </div>
                                                     </form>
                                                 </div>
                                             </div>
                                         </div>
                                     </div>
                                 </td>
                             </tr>
                            @empty
                                <tr>
                                    <td>
                                        <h4 class="fucsia">La sede non ha dipendenti</h4>
                                    </td>
                                </tr>
                            @endforelse
                            <tr>
                                <td>
                                    <div class="row">
                                        <div class="col-md-8 push-2">
                                            {{$employee->links('vendor.pagination.bootstrap-4')}}
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