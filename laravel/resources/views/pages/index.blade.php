@extends('layouts.app')

@section('content')

    <div class="d-flex justify-content-between align-items-center">
        <h1>Feladatok</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">Új feladat
        </button>
    </div>
    <div class="row mt-5">
        @foreach($tasks as $task)
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title">
                            {{$task->title}}
                        </h5>
                        <h6 class="card-subtitle mb-2 {{$status[$task->status]['class']}}">
                            {{$status[$task->status]['title']}}
                        </h6>
                        <p class="card-text">
                            {{$task->description}}
                        </p>
                        <div class="d-flex gap-2">
                            @if($task->status != App\Models\Task::DONE)
                                @php
                                    $next = $task->status == App\Models\Task::NEW ? App\Models\Task::IN_PROGRESS : App\Models\Task::DONE;
                                @endphp
                                <form method="post"
                                      action="{{route('task.status',$task)}}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="{{$next}}">
                                    <input type="submit" class="btn btn-outline-primary"
                                           value="{{$status[$next]['title']}}"/>
                                </form>
                                <a href="{{route('task.edit', $task)}}" class="btn btn-outline-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            @endif
                            <form method="post"
                                  action="{{route('task.destroy',$task)}}">
                                @csrf
                                @method('delete')
                                <button type="submit" name="delete" class="btn btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        <!-- Modal -->
        <div class="modal @if($errors->isEmpty()) fade @endif" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Új feladat</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="post" action="{{route('task.store')}}">
                            @csrf
                            @method("post")
                            <div class="mb-3">
                                <label for="title" class="form-label">Cím</label>
                                <input type="text" id="title" name="title" value="{{old('title')}}"
                                       class="form-control @error('title') is-invalid @enderror">
                                @error('title')
                                <div class="invalid-feedback">
                                    {{$message}}
                                </div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Leírás</label>
                                <textarea id="description" name="description"
                                          class="form-control @error('description') is-invalid @enderror"
                                          rows="10">{{old('description')}}</textarea>
                                @error('description')
                                <div class="invalid-feedback">
                                    {{$message}}
                                </div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary">Mentés</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

@endsection

@push('scripts')
    @if($errors->any())
        <script>
            const myModal = new bootstrap.Modal(document.getElementById('createModal'), {});
            myModal.toggle()
        </script>
    @endif
@endpush
