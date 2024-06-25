
<div class="tab-pane {{ !empty($active) || !empty($is_new) ? 'show active' : '' }}" id="{{ $type ?? "new" }}">
    <form class="form-horizontal" action="{{ route('settings.email-templates.save') }}" method="POST">
        @csrf
        @isset($id)
        <input type="hidden" name="id" value="{{ $id }}">
        @endisset
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>{{ __('Name') }}</label>
                            <input type="text" name="name" class="form-control" id="name" value="{{ $name ?? ""}}" @if (empty($is_new)) disabled @endif>
                            <x-forms.error name="name" />
                        </div>
                        @if (!empty($is_new))
                            <div class="form-group">
                                <label>{{ __('Type') }}</label>
                                <input type="text" name="type" class="form-control" id="type" value="{{ $type ?? ""}}">
                                <x-forms.error name="type" />
                            </div>
                        @endif
                        <div class="form-group">
                            <label>{{ __('Subject') }}</label>
                            <input type="text" name="subject" class="form-control" id="subject" value="{{ $subject ?? ""}}">
                            <x-forms.error name="subject" />
                        </div>
                        <div class="form-group">
                            <label>{{ __('Message') }}</label>
                            <textarea name="message" id="message" class="form-control classic-editor" cols="30" rows="10">{{ $message ?? ""}}</textarea>
                            <x-forms.error name="message" />
                        </div>
                        @if (!empty($flags["search"]))
                        <small>
                            {{ __("Available flags:") }}
                            @foreach ($flags["search"] as $flag)
                                <code>{{ $flag }}</code> @if (!$loop->last) , @else . @endif
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
