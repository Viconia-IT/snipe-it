<!-- Article Number -->
<div class="form-group {{ $errors->has('article_number') ? ' has-error' : '' }}">
   <label for="article_number" class="col-md-3 control-label">{{ trans('general.article_number') }}</label>
   <div class="col-md-7 col-sm-12">
       <input class="form-control" type="text" name="article_number" aria-label="article_number" id="article_number" value="{{ old('article_number', $item->article_number) }}" />
       {!! $errors->first('article_number', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
   </div>
</div>
