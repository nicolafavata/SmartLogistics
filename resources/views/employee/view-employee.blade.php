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
                                        Ruoli
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
                                     {{$employees->cognome.' '.$employees->name}}
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
                                 <td class="capitalize">
                                     @if($employees->responsabile=='1') {{'Responsabile: '}} @endif
                                     @if($employees->acquisti=='1') {{'acquisti '}} @endif
                                         @if($employees->produzione=='1') {{'produzione '}} @endif
                                         @if($employees->vendite=='1') {{'vendite '}} @endif
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