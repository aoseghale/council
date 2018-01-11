@extends('layouts.app')

@section('header')
    <link rel="stylesheet" href="{{ asset('css/vendor/jquery.atwho.css') }}"/>
@endsection

@section('content')
    <thread-view :data-thread="{{ $thread }}" inline-template>
        <div class="container">
            <div class="row">
                <div class="col-md-8" v-cloak>
                    @include('threads._main')

                    <replies @added="repliesCount++" @removed="repliesCount--"></replies>

                    {{--{{ $replies->links() }}--}}

                    {{--@if  (auth()->check())--}}
                        {{--<form method="POST" action="{{ $thread->path() . '/replies' }}">--}}
                        {{--{{ csrf_field()  }}--}}

                        {{--<!-- Body Form TextArea -->--}}
                            {{--<div class="form-group">--}}
                        {{--<textarea name="body" id="body" class="form-control"--}}
                                  {{--placeholder="Have something to say?"--}}
                                  {{--rows="5"></textarea>--}}
                            {{--</div>--}}

                            {{--<!--  Form Input -->--}}
                            {{--<div class="form-group">--}}
                                {{--<button type="submit" class="btn btn-default">Post</button>--}}
                            {{--</div>--}}
                        {{--</form>--}}
                    {{--@else--}}
                        {{--<p class="text-center">Please <a href="{{ route('login') }}">sign in</a> to participate in this--}}
                            {{--discussion.</p>--}}
                    {{--@endif--}}
                </div>
                <div class="col-md-4">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <p>
                                This thread was published {{ $thread->created_at->diffForHumans() }} by <a
                                        href="#">{{ $thread->creator->name }}</a>, and currently
                                has <span v-text="repliesCount"></span> {{ str_plural('comment', $thread->replies_count) }}.
                            </p>

                            <p>
                                <subscribe-button :active="{{ json_encode($thread->isSubscribedTo) }}"
                                                  v-if="signedIn"></subscribe-button>

                                <button class="btn btn-default"
                                        v-if="authorize('isAdmin')"
                                        @click="toggleLock"
                                        v-text="locked ? 'Unlock' : 'Lock'"></button>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </thread-view>
@endsection
