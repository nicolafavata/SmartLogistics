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
                                             Modifica dipendente
                                         </button>
                                         <!-- Modal -->
                                         <div class="modal fade hide" id="exampleModal{{$employees->user_employee}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                             <div class="modal-dialog" role="document">
                                                 <div class="modal-content">
                                                     <div class="form-check">
                                                         <form onsubmit="showloader()" method="POST" action="{{ route('update-employee') }}">
                                                             {{ csrf_field() }}
                                                             <input type="hidden" name="_method" value="PATCH">
                                                             <input type="hidden" name="user_employee" value="{{$employees->user_employee}}">
                                                             <div><br /></div>
                                                             <div class="form-check">
                                                                 <label class="grigio">Nome:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</label>
                                                                 <input required maxlength="255" placeholder="Nome dell'impiegato" type="text" name="name" id="name" class="font-weight-bold" value="{{old('name',$employees->name)}}" >
                                                             </div><br />
                                                             <div class="form-check">
                                                                 <label class="grigio">Cognome:&nbsp</label>
                                                                 <input required maxlength="255" placeholder="Cognome dell'impiegato" type="text" name="cognome" id="cognome" class="font-weight-bold" value="{{old('cognome',$employees->cognome)}}" >
                                                             </div><br />
                                                             <div class="form-check">
                                                                 <label class="fucsia">Seleziona i privilegi&nbsp</label>
                                                                 <div>
                                                                     <input type="checkbox" name="responsabile" value="1"
                                                                            @if($employees->responsabile=='1')
                                                                                checked
                                                                            @endif
                                                                     ><label class="grigio">&nbsp&nbspResponsabile</label>
                                                                 </div>
                                                                 <div>
                                                                     <input type="checkbox" name="acquisti" value="1"
                                                                            @if($employees->acquisti=='1')
                                                                            checked
                                                                             @endif
                                                                     ><label class="grigio">&nbsp&nbspAcquisti</label>
                                                                 </div>
                                                                 <div>
                                                                     <input type="checkbox" name="produzione" value="1"
                                                                            @if($employees->produzione=='1')
                                                                            checked
                                                                             @endif
                                                                     ><label class="grigio">&nbsp&nbspProduzione</label>
                                                                 </div>
                                                                 <div>
                                                                     <input type="checkbox" name="vendite" value="1"
                                                                            @if($employees->vendite=='1')
                                                                            checked
                                                                             @endif
                                                                     ><label class="grigio">&nbsp&nbspVendite</label>
                                                                 </div>
                                                             </div><br />
                                                             <div class="form-check">
                                                                 <label class="grigio">Matricola:&nbsp&nbsp&nbsp&nbsp</label>
                                                                 <input maxlength="16" placeholder="Identificativo" type="text" name="matricola" id="matricola" class="font-weight-bold uppercase form-group"
                                                                        value="{{old('matricola',$employees->matricola)}}" >
                                                             </div><br />
                                                             <div class="form-check">
                                                                 <label class="grigio">Telefono:&nbsp&nbsp&nbsp&nbsp&nbsp</label>
                                                                 <input maxlength="16" placeholder="0277841256" type="text" name="tel_employee" id="tel_employee" class="font-weight-bold uppercase form-group"
                                                                        value="{{old('tel_employee',$employees->tel_employee)}}" >
                                                             </div><br />
                                                             <div class="form-check">
                                                                 <label class="grigio">Mobile:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</label>
                                                                 <input placeholder="+393485517452" type="text" name="cell_employee" id="cell_employee" class="font-weight-bold uppercase form-group"
                                                                        maxlength="16" value="{{old('cell_employee',$employees->cell_employee)}}" >
                                                             </div><br />
                                                             <div>
                                                                 <button type="submit" class="btn btn-primary pulsante" id="submit_profile">
                                                                     Aggiorna
                                                                 </button>
                                                             </div>
                                                             <div><br /></div>
                                                         </form>
                                                     </div>
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