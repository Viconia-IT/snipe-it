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
        <!-- Invoice Id -->
        <div class="form-group {{ $errors->has('invoice_id') ? ' has-error' : '' }}">
          <label for="invoice_id" class="col-md-3 control-label">
            Invoice Id
          </label>
          <div class="col-md-7{{  (Helper::checkIfRequired($item, 'invoice_id')) ? ' required' : '' }}">
            <input class="form-control" type="text" name="invoice_id" id="invoice_id" value="{{ old('invoice_id', $item->invoice_id) }}" />
            {!! $errors->first('invoice_id', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
          </div>
        </div>

        <!-- Articles + button -->
        <div class="form-group {{ $errors->has('articles') ? ' has-error' : '' }}">
            <label for="articles" class="col-md-3 control-label">Articles</label>
            <div class="col-md-2 col-sm-12">
                <button class="add_field_button btn btn-default btn-sm">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </div>
        <div class="input_fields_wrap"></div>
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
        var articleIndex    = 0; //Index in the array
        var x               = 1; //initial text box count
        var articleTypes    = <?php echo json_encode($articleTypes); ?>;
        

        // Those we got from the server on page load
        // If we edit an existing maintenance there may already exist some articles
        function populateExistingArticles()
        {
            var articles = <?php echo isset($item->articles) ? $item->articles : json_encode([]); ?>;
            articles.forEach(element => {
                addArticleField(element);
            });

        }

        function addArticleField(defaultValue)
        {
            x++; //text box increment

            var box_html = '';
            box_html += '<span class="fields_wrapper">';
            box_html += '<div class="form-group">';
            box_html += '<label for="articles[' + articleIndex + ']" class="col-md-3 control-label">#'+ articleIndex +'</label>'
            box_html += '<div class="col-md-7 col-sm-12 required">';
            
            // Use a normal select list to choose
            box_html += '<select type="text" class="myselect form-control" name="articles[' + articleIndex + ']">';
            articleTypes.forEach(element => {
                var selected = (element === defaultValue) ? "selected" : "";
                box_html += '<option class="myselect form-control" style="min-width:350px" "aria-label"="ArticleTypes" value="' + element + '" ' + selected +' >' + element + '</option>';
            });
            box_html += '</select>';

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

        $(add_button).click(function(e){ //on add input button click
            e.preventDefault();
            addArticleField("defaultValue");
        });
        
        $(wrapper).on("click",".remove_field", function(e){ //user clicks on remove text
            e.preventDefault();
            $(this).parent('div').parent('div').parent('span').remove();
            x--;
        });


        populateExistingArticles();
    });

</script>
@stop

<!--   VICONIA END   -->