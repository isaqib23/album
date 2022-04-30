@extends('layouts.app')

@section('content')

    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Dashboard</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{url('/')}}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Posts</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Posts</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped projects">
                    <thead>
                    <tr>
                        <th style="width: 1%">
                            #
                        </th>
                        <th style="width: 20%">
                            Post Images
                        </th>
                        <th style="width: 30%">
                            Post Caption
                        </th>
                        <th>
                            Created By
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($posts as $post)
                        <tr>
                            <td>
                                #
                            </td>
                            <td>
                                <ul class="list-inline">
                                    @if($post->images)
                                    @foreach($post->images as $img)
                                        <li class="list-inline-item">
                                            <img alt="Avatar" class="table-avatar" src="{{$img->image}}">
                                        </li>
                                    @endforeach
                                    @endif
                                </ul>
                            </td>
                            <td>
                                <a>
                                    {{$post->name}}
                                </a>
                                <br/>
                                <small>
                                    Created {{date('Y-m-d', strtotime($post->created_at))}}
                                </small>
                            </td>
                            <td class="project-state">
                                <span>{{$post->created_by_name}}</span>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->

    </section>
    <!-- /.content -->

@endsection
