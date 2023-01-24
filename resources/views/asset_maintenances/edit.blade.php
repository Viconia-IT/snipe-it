@extends('layouts/default')

{{-- Page title --}}
@section('title')
  @if ($item->id)
    {{ trans('admin/asset_maintenances/form.update') }}
  @else
    {{ trans('admin/asset_maintenances/form.create') }}
  @endif
  @parent
@stop


@section('header_right')
<a href="{{ URL::previous() }}" class="btn btn-primary pull-right">
  {{ trans('general.back') }}</a>
@stop


{{-- Page content --}}
@section('content')

<div class="row">
  <div class="col-md-9">
    @if ($item->id)
      <form class="form-horizontal" method="post" action="{{ route('maintenances.update', $item->id) }}" autocomplete="off">
      {{ method_field('PUT') }}
    @else
      <form class="form-horizontal" method="post" action="{{ route('maintenances.store') }}" autocomplete="off">
    @endif
    <!-- CSRF Token -->
    {{ csrf_field() }}

    <div class="box box-default">
      <div class="box-header with-border">
        <h2 class="box-title">
          @if ($item)
          {{ $item->name }}
          @endif
        </h2>
      </div><!-- /.box-header -->

      <div class="box-body">
        @include ('partials.forms.edit.asset-select', ['translated_name' => trans('admin/hardware/table.asset_tag'), 'fieldname' => 'asset_id', 'required' => 'true'])
        @include ('partials.forms.edit.supplier-select', ['translated_name' => trans('general.supplier'), 'fieldname' => 'supplier_id', 'required' => 'true'])
        @include ('partials.forms.edit.maintenance_type')

        <!-- Title -->
        <div class="form-group {{ $errors->has('title') ? ' has-error' : '' }}">
          <label for="title" class="col-md-3 control-label">
            {{ trans('admin/asset_maintenances/form.title') }}
          </label>
          <div class="col-md-7{{  (Helper::checkIfRequired($item, 'title')) ? ' required' : '' }}">
            <input class="form-control" type="text" name="title" id="title" value="{{ old('title', $item->title) }}" />
            {!! $errors->first('title', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
          </div>
        </div>

        <!-- Start Date -->
        <div class="form-group {{ $errors->has('start_date') ? ' has-error' : '' }}">
          <label for="start_date" class="col-md-3 control-label">{{ trans('admin/asset_maintenances/form.start_date') }}</label>

          <div class="input-group col-md-3{{  (Helper::checkIfRequired($item, 'start_date')) ? ' required' : '' }}">
            <div class="input-group date" data-provide="datepicker" data-date-format="yyyy-mm-dd"  data-autoclose="true" data-date-clear-btn="true">
              <input type="text" class="form-control" placeholder="{{ trans('general.select_date') }}" name="start_date" id="start_date" value="{{ old('start_date', $item->start_date) }}">
              <span class="input-group-addon"><i class="fas fa-calendar" aria-hidden="true"></i></span>
            </div>
            {!! $errors->first('start_date', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
          </div>
        </div>



        <!-- Completion Date -->
        <div class="form-group {{ $errors->has('completion_date') ? ' has-error' : '' }}">
          <label for="start_date" class="col-md-3 control-label">{{ trans('admin/asset_maintenances/form.completion_date') }}</label>

          <div class="input-group col-md-3{{  (Helper::checkIfRequired($item, 'completion_date')) ? ' required' : '' }}">
            <div class="input-group date" data-date-clear-btn="true" data-provide="datepicker" data-date-format="yyyy-mm-dd"  data-autoclose="true">
              <input type="text" class="form-control" placeholder="{{ trans('general.select_date') }}" name="completion_date" id="completion_date" value="{{ old('completion_date', $item->completion_date) }}">
              <span class="input-group-addon"><i class="fas fa-calendar" aria-hidden="true"></i></span>
            </div>
            {!! $errors->first('completion_date', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
          </div>
        </div>

        <!-- Warranty -->
        <div class="form-group">
          <div class="col-sm-offset-3 col-sm-9">
            <div class="checkbox">
              <label>
                <input type="checkbox" value="1" name="is_warranty" id="is_warranty" {{ Request::old('is_warranty', $item->is_warranty) == '1' ? ' checked="checked"' : '' }} class="minimal"> {{ trans('admin/asset_maintenances/form.is_warranty') }}
              </label>
            </div>
          </div>
        </div>

        <!-- Asset Maintenance Cost -->
        <div class="form-group {{ $errors->has('cost') ? ' has-error' : '' }}">
          <label for="cost" class="col-md-3 control-label">{{ trans('admin/asset_maintenances/form.cost') }}</label>
          <div class="col-md-2">
            <div class="input-group">
              <span class="input-group-addon">
                @if (($item->asset) && ($item->asset->location) && ($item->asset->location->currency!=''))
                  {{ $item->asset->location->currency }}
                @else
                  {{ $snipeSettings->default_currency }}
                @endif
              </span>
              <input class="col-md-2 form-control" type="text" name="cost" id="cost" value="{{ old('cost', Helper::formatCurrencyOutput($item->cost)) }}" />
              {!! $errors->first('cost', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
            </div>
          </div>
        </div>

        <!-- Notes -->
        <div class="form-group {{ $errors->has('notes') ? ' has-error' : '' }}">
          <label for="notes" class="col-md-3 control-label">{{ trans('admin/asset_maintenances/form.notes') }}</label>
          <div class="col-md-7">
            <textarea class="col-md-6 form-control" id="notes" name="notes">{{ old('notes', $item->notes) }}</textarea>
            {!! $errors->first('notes', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
          </div>
        </div>
        
<!--   VICONIA START   -->
@can('editMaintenanceArticles', \App\Models\Asset::class)
        <hr style="border-top: 1px solid rgba(0,0,0, 0.1);">   

        <!-- Internal Notes -->
        <div class="form-group {{ $errors->has('internal_notes') ? ' has-error' : '' }}">
          <label for="internal_notes" class="col-md-3 control-label">Internal Notes</label>
          <div class="col-md-7">
            <textarea class="col-md-6 form-control" id="internal_notes" name="internal_notes">{{ old('internal_notes', $item->internal_notes) }}</textarea>
            {!! $errors->first('internal_notes', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
          </div>
        </div>

        <!-- Ready for Billing -->
        <div class="form-group">
          <div class="col-sm-offset-3 col-sm-9">
            <div class="checkbox">
              <label>
                <input type="checkbox" value="1" name="ready_for_billing" id="ready_for_billing" {{ Request::old('ready_for_billing', $item->ready_for_billing) == '1' ? ' checked="checked"' : '' }} class="minimal"> Ready for Billing
              </label>
            </div>
          </div>
        </div>

        <!-- Invoice Id -->
        <div class="form-group {{ $errors->has('invoice_id') ? ' has-error' : '' }}">
          <label for="invoice_id" class="col-md-3 control-label">
            Invoice Id
          </label>
          <div class="col-md-7{{  (Helper::checkIfRequired($item, 'invoice_id')) ? ' required' : '' }}">
            <input class="form-control" type="text" name="invoice_id" id="invoice_id" placeholder="After the maintenance is exported for billing this should reference the invoice/ticket" value="{{ old('invoice_id', $item->invoice_id) }}" />
            {!! $errors->first('invoice_id', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
          </div>
        </div>

        <!-- Articles + button -->
        <div class="form-group {{ $errors->has('articles') ? ' has-error' : '' }}">
            <label for="articles" class="col-md-3 control-label">Articles</label>
            <div class="col-md-7 col-sm-12">
                <select id="article-select" class="js-data-ajax select2" data-endpoint="components" data-placeholder="Select article to add" aria-label="articles" name="articles" style="width: 100%">
                    <option value="" selected="selected" role="option" aria-selected="true" role="option">
                    </option>
                </select>
            </div>
        </div>
        
        <div class="input_fields_wrap"></div>
@endcan
<!--   VICONIA END   -->


      </div> <!-- .box-body -->

      <div class="box-footer text-right">
        <button type="submit" class="btn btn-success"><i class="fas fa-check icon-white" aria-hidden="true"></i> {{ trans('general.save') }}</button>
      </div>
    </div> <!-- .box-default -->
    </form>
  </div>
</div>

@stop

<!--   VICONIA START   -->
@section('moar_scripts')
<script>
    
    // Add another asset tag + serial combination if the plus sign is clicked
    $(document).ready(function() {

        var wrapper         = $(".input_fields_wrap"); //Fields wrapper
        var add_button      = $(".add_field_button"); //Add button ID
        var articleIndex    = 1; //Index in the array
        var x               = 0; //initial text box count
        

        // Those we got from the server on page load
        // If we edit an existing maintenance there may already exist some articles
        function populateExistingArticles()
        {
            var articles = <?php echo isset($item->articles) ? $item->articles : json_encode([]); ?>;
            articles.forEach(element => {
                let string = element.article_nr + " - " + element.component_name + " (" + element.component_id + ")";
                addArticleField(string);
            });

        }

        function addArticleField(value)
        {
            x++; //text box increment

            var box_html = '';
            box_html += '<span class="fields_wrapper">';
            box_html += '<div class="form-group">';
            box_html += '<label for="articles[' + articleIndex + ']" class="col-md-3 control-label">#'+ articleIndex +'</label>'
            box_html += '<div class="col-md-7 col-sm-12">';

            box_html += '<input class="form-control"  name="articles[' + articleIndex + ']" value="'+ value +'" readonly />';
        

            box_html += '</div>';
            box_html += '<div class="col-md-2 col-sm-12">';
            box_html += '<a href="#" class="remove_field btn btn-default btn-sm"><i class="fas fa-minus"></i></a>';
            box_html += '</div>';
            box_html += '</div>';
            box_html += '</div>';
            box_html += '</span>';

            $(wrapper).append(box_html);
            $('.myselect').select2();   //Initialize Select2 Elements

            articleIndex++;
        }
        
        $(wrapper).on("click",".remove_field", function(e){ //user clicks on remove text
            e.preventDefault();
            $(this).parent('div').parent('div').parent('span').remove();
            x--;
        });

        $("#article-select").on("change", function(e) {

            // Testing
            //var elm = document.getElementById("article-select");
            //var value = elm.value;
            //for (let i = 0; i < elm.options.length; i++) {
            //    const element = elm.options[i];
            //    window.alert(i + " - " + elm.options[i].text);
            //}
            //window.alert(elm.options[value].text);

            var elm = document.getElementById("article-select");
            if (x < 50) addArticleField(elm.options[1].text);
            else        window.alert("You can't add more than 50 articles");
           
            // Clear selected value and the options
            // The options from the server will reload each time, and when selected, add itself as a real HTML option
            // This real option needs to be removed so we can know which option is selected next time
            elm.value = "";
            elm.options[1] = null;

        });


        populateExistingArticles();
    });

</script>
@stop

<!--   VICONIA END   -->