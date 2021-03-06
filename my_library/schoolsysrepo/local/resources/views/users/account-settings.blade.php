
@extends($layout)

@section('content')
<div id="page-wrapper">
			<div class="container-fluid">
				<!-- Page Heading -->
				<div class="row">
					<div class="col-lg-12">
						<ol class="breadcrumb">
							<li><a href="{{PREFIX}}"><i class="mdi mdi-home"></i></a> </li>
							@if(checkRole(getUserGrade(2)))
							<li><a href="{{URL_USERS}}">{{ getPhrase('users')}}</a> </li>
							<li class="active">{{isset($title) ? $title : ''}}</li>
							@else
							<li class="active">{{$title}}</li>
							@endif
						</ol>
					</div>
				</div>
					@include('errors.errors')
				<!-- /.row -->

				<?php
				$user_options = null;
				if($record->settings)
					$user_options = json_decode($record->settings)->user_preferences;
				?>
	<div class="panel panel-custom col-lg-12" >
					<div class="panel-heading">
					@if(checkRole(getUserGrade(2)))
						<div class="pull-right messages-buttons">

							<a href="{{URL_USERS}}" class="btn  btn-primary button" >{{ getPhrase('list')}}</a>

						</div>
						@endif
					<h1>{{ $title }}  </h1>
					</div>


					<div class="panel-body">

					 <?php $button_name = getPhrase('update'); ?>
						{{ Form::model($record,
						array('url' => URL_USERS_SETTINGS.$record->slug,
						'method'=>'patch','novalidate'=>'','name'=>'formUsers ', 'files'=>'true' )) }}
						 @if(count($quiz_categories) != 0)
					<h1>{{getPhrase('exam_categories')}}</h1>
						 @endif
					<div class="row">
					@foreach($quiz_categories as $category)
 				<?php

	 					$checked = '';
	 					if($user_options) {
	 						if(count($user_options->quiz_categories))
	 						{
	 							if(in_array($category->id,$user_options->quiz_categories))
	 								$checked='checked';
	 						}
	 					}
 					?>
					<div class="col-md-3">
						<label class="checkbox-inline" >
							<input type="checkbox" data-toggle="toggle" name="quiz_categories[{{$category->id}}]" data-onstyle="primary" data-offstyle="default" {{$checked}}> {{$category->category}}
						</label>
					</div>
					@endforeach

				 </div>
						 @if(count($offline_category) != 0)
						 <h1> {{getPhrase('offline_exam_categories')}}</h1>
						 @endif

						 <div class="row">
							 @foreach($offline_category as $category)
                                 <?php

                                 $checked = '';
                                 if($user_options) {
                                     if(count($user_options->offline_categories))
                                     {
                                         if(in_array($category->id,$user_options->offline_categories))
                                             $checked='checked';
                                     }
                                 }
                                 ?>
								 <div class="col-md-3">
									 <label class="checkbox-inline">
										 <input 	type="checkbox"
												   data-toggle="toggle"
												   data-onstyle="primary"
												   data-offstyle="default"
												   name="offline_categories[{{$category->id}}]"
												 {{$checked}}
										 > {{$category->title}}
									 </label>
								 </div>
							 @endforeach

						 </div>
						 @if(count($lms_category) != 0)
				 	<h1> {{getPhrase('lms_categories')}}</h1>
					@endif
					<div class="row">
					@foreach($lms_category as $category)
 					<?php

	 					$checked = '';
	 					if($user_options) {
	 						if(count($user_options->lms_categories))
	 						{
	 							if(in_array($category->id,$user_options->lms_categories))
	 								$checked='checked';
	 						}
	 					}
 					?>
					<div class="col-md-3">
						<label class="checkbox-inline">
							<input 	type="checkbox"
									data-toggle="toggle"
									data-onstyle="primary"
									data-offstyle="default"
									name="lms_categories[{{$category->id}}]"
									{{$checked}}
									> {{$category->category}}
						</label>
					</div>
					@endforeach

				 </div>

				 <div class="buttons text-center">
							<button class="btn btn-lg btn-primary button"
							>{{ getPhrase('update') }}</button>
						</div>

					{!! Form::close() !!}
					</div>
				</div>
			</div>
			<!-- /.container-fluid -->
		</div>
		<!-- /#page-wrapper -->
@endsection

@section('footer_scripts')
 @include('common.validations');
 <script src="{{JS}}bootstrap-toggle.min.js"></script>
@stop
