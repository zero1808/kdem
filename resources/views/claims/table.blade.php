<div class="row">
    <div class="col-sm-12">
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover" id="claims">
                <thead>
                    <tr>
                        <th>VIN</th>
                        <th>Modelo</th>
                        <th>Carrier</th>
                        <th>Fechas de arribo</th>
                        @if(Auth::user()->rol->name == \Config::get('constants.options.ROL_ADMINISTRATOR'))
                        <th>Dealer</th>
                        @endif
                        <th>Men&uacute;</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

