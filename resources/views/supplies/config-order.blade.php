@extends('layouts.employee-sub')
@section('title','Smartlogis per le aziende')

@section('content_header')
    <div class="content">
        <h2>{{$provider->rag_soc_provider}}</h2>
        <h3 class="grigio">{{$provider->address_provider.' - tel. '.$provider->telefono_provider.' - email. '.$provider->email_provider}}</h3>
    </div>
@endsection

@section('content_section')
        <div class="container-fluid home_employee ship_img">
                <div class="row">
                    <div class="col-md-12 jumbotron border employee">
                        <div class="row">
                                 <form onsubmit="showloader()" method="POST" action="{{ route('setting-config-order')}}">
                                    {{ csrf_field() }}
                                     <input type="hidden" name="_method" value="PATCH">
                                     <input type="hidden" name="provider_config_order" value="{{$config->provider_config_order}}">
                                    <div class="center">
                                        <h2 class="fucsia shadow">Configurazione ordini d'acquisto</h2>
                                    </div>

                                        <div class="form-check">
                                            <label class="grigio">Lead time</label>
                                            <input type="text" id="text-lead-time" min="0" max="365" value="{{old('lead_time_config',$config->lead_time_config)}}" onchange="updateText('lead_time_config',this.value)"><br />
                                            <input type="range" name="lead_time_config" id="lead_time_config" min="0" max="365" value="{{old('lead_time_config',$config->lead_time_config)}}" onchange="updateText('text-lead-time',this.value)" >
                                        </div>
                                     <hr>
                                        <div class="form-check">
                                             <label class="verde">Finestra mensile di esecuzione ordini</label><br />
                                            <table>
                                                <tr>
                                                    <td>
                                                        <label class="grigio">Dal giorno</label>
                                                    </td>
                                                    <td>
                                                        <label class="grigio">Al giorno</label>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <input type="text" id="text-window_first_config" value="{{old('window_first_config',$config->window_first_config)}}" onchange="updateText('window_first_config',this.value)">
                                                    </td>
                                                    <td>
                                                        <input type="text" id="text-window_last_config" value="{{old('window_last_config',$config->window_last_config)}}" onchange="updateText('window_last_config',this.value)">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <input type="range" name="window_first_config" id="window_first_config" min="1" max="31" value="{{old('window_first_config',$config->window_first_config)}}" onchange="updateText('text-window_first_config',this.value)" >
                                                    </td>
                                                    <td>
                                                        <input type="range" name="window_last_config" id="window_last_config" min="1" max="31" value="{{old('window_last_config',$config->window_last_config)}}" onchange="updateText('text-window_last_config',this.value)" >
                                                    </td>
                                                </tr>
                                            </table>
                                        </div><hr>
                                     <div class="form-check">
                                         <label class="verde">Importo minimo e importo massimo dell'ordine</label><br />
                                         <table>
                                             <tr>
                                                 <td>
                                                     <label class="grigio">Da €</label>
                                                 </td>
                                                 <td>
                                                     <label class="grigio">A €</label>
                                                 </td>
                                             </tr>
                                             <tr>
                                                 <td>
                                                     <input type="number" step="0.10" name="min_import_config" id="min_import_config" value="{{old('min_import_config',$config->min_import_config)}}" onchange="checkImportMax('max_import_config',this.value)">
                                                 </td>
                                                 <td>
                                                     <input type="number" step="0.10" name="max_import_config" id="max_import_config" value="{{old('max_import_config',$config->max_import_config)}}" onchange="checkImportMin('min_import_config',this.value)">
                                                 </td>
                                             </tr>
                                         </table>
                                     </div><hr>
                                     <div>
                                         <label class="verde">Prodotti da ordinare</label><br />
                                         <input type="radio"  name="mapping_config" value="01" checked><label class="grigio">&nbsp;Principali</label>
                                         <input type="radio"  name="mapping_config" value="10"
                                                @if($config->mapping_config=="10") checked @endif
                                         ><label class="grigio">&nbsp;Secondari</label>
                                         <input type="radio"  name="mapping_config" value="11"
                                                @if($config->mapping_config=="11") checked @endif
                                         ><label class="grigio">&nbsp;Misti</label>
                                     </div>
                                     <hr>
                                     <div>
                                         <label class="verde">Trasmissione ordine</label><br />
                                         <input type="radio"  name="transmission_config" value="1" checked><label class="grigio">&nbsp;Automatica</label>
                                         <input type="radio"  name="transmission_config" value="0"
                                                @if($config->transmission_config=="0") checked @endif
                                         ><label class="grigio">&nbsp;Manuale</label>
                                     </div>
                                     <hr>
                                     <div>
                                         <label class="verde">Evento generazione ordine</label><br />
                                         <input type="checkbox"  name="level_config" value="1"
                                                @if($config->level_config=='1') checked @endif
                                         ><label class="grigio">La disponibilità è al di sotto del livello di sicurezza</label><br />
                                         <input type="radio"  name="execute_config" value="0" checked onclick="NoShowId('days_number')"><label class="grigio" >&nbsp;Dall'inizio del mese</label><br />
                                         <input type="radio"  name="execute_config" value="1"
                                                @if($config->execute_config=="1") checked @endif
                                         onclick="showId('days_number')"><label class="grigio">&nbsp;Specifica numero di giorni</label>
                                         <div id="days_number" @if($config->days_number_config==0)
                                             hidden
                                            @endif>
                                             <label class="grigio">Numero di giorni</label>
                                             <input type="number" name="days_number_config" id="days_number_config" value="{{old('days_number_config',$config->days_number_config)}}" onchange="updateText('days_number_config',this.value)"><br />
                                         </div>
                                     </div>
                                     <hr>
                                     <div class="destra">
                                             <button type="submit" class="btn btn-primary pulsante" id="submit_profile">
                                                 Conferma
                                             </button>
                                     </div>
                                 </form>
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