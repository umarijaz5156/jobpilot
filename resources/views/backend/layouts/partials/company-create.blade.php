@php
$name_label = $name_label ?? "Name"; // snake or kebab case
$subject_label = $subject_label ?? "Subject"; // text label
$show_class = !empty($active) || $is_new ? "show active" : "";
@endphp

<div class="tab-pane {{ $show_class }}" id="item-{{ isset($mail_template) ? $mail_template->id : "new" }}">
    <form class="form-horizontal" action="{{ route("settings.mail-templates.store") }}" method="POST" enctype="multipart/form-data">
        @csrf
        @isset($id)
        <input type="hidden" name="id" value="{{ $id }}">
        @endisset
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>{{ __("name") }}</label>
                            <input type="text" class="form-control @error("name") is-invalid @enderror" id="{{ "name" }}"
                                value="{{ $name }}" name="name" @if(empty($is_new) || isset($id)) disabled @endif />
                            <x-forms.error name="name" />
                        </div>
                        @if(!empty($is_new))
                        <div class="form-group">
                            <label>{{ __("type") }}</label>
                            <input type="text" class="form-control @error("type") is-invalid @enderror" id="type"
                                value="{{ $name }}" name="type" @isset($id) disabled @endisset />
                            <x-forms.error name="type" />
                        </div>
                        @endif
                        <div class="form-group">
                            <label>{{ __("subject") }}</label>
                            <input type="text" class="form-control @error("subject") is-invalid @enderror" id="subject"
                                value="{{ $subject }}" name="subject">
                            <x-forms.error :name="'subject'" />
                        </div>
                        <div class="form-group">
                            <textarea class="form-control {{ $errors->has("message") ? 'is-invalid' : '' }}"
                                rows="5" name="message">{{ $message }}</textarea>
                            <x-forms.error name="message" />
                        </div>
                        @if (!empty($available_flags))
                        <small>
                            {{ __("Available flags: ") }}
                            @foreach ($available_flags as $flag)
                                <code>{{ $flag }}</code>
                                @if (!$loop->last), @else . @endif
                            @endforeach
                        </small>
                        @endif
                    </div>
                </div>
                @if (userCan('setting.update'))
                    <div class="row mt-3 mx-auto justify-content-center">
                        <button type="submit" class="btn btn-success"><i class="fas fa-sync"></i>
                            {{ __('update') }}
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </form>
</div>
