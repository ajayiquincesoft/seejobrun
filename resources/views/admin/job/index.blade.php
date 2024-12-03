@extends('layouts.app')

@section('content')
  <div class="header pb-6">
    <div class="container-fluid">
      <div class="header-body">
        <div class="row align-items-center py-4">
          <div class="col-lg-6 col-7">
            <h6 class="h2 text-white d-inline-block mb-0">Job Information</h6>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="container-fluid mt--6">
    <div class="row">
      <div class="col">
        <div class="card bg-default shadow">
          <div class="card-header bg-transparent border-0">
            @if(session('success'))
            <div class="alert alert-success">{{session('success')}}</div>
            @endif
          </div>

          <div class="col-md-12">
            <form action="{{ route('job.index') }}" method="GET" class="mb-2">
              <div class="row">
                <div class="col-md-4">
                  <input type="text" class="form-control" name="search_name" id="search_name" placeholder="Search" value="{{ request()->search_name }}">
                </div>

                <div class="col-md-2">
                  <select id="search_jobtype" class="form-control" name="search_jobtype">
                    <option value="" @if (request()->search_jobtype == "") selected @endif>All</option>
                    <option value="Residential" @if (request()->search_jobtype == "Residential") selected @endif>Residential</option>
                    <option value="Commercial" @if (request()->search_jobtype == "Commercial") selected @endif>Commercial</option>
                    <option value="Leads" @if (request()->search_jobtype == "Leads") selected @endif>Leads</option>
                    <option value="Drafts" @if (request()->search_jobtype == "Drafts") selected @endif>Drafts</option>
                    <option value="Archived" @if (request()->search_jobtype == "Archived") selected @endif>Archived</option>
                  </select>
                </div>

                <div class="col-md-2">
                  <select id="search_user" class="form-control" name="search_user">
                    <option value="" @if (request()->search_user == "") selected @endif>All</option>
                    @foreach($user as $details)
                      <option value="{{$details->id}}" @if (request()->search_user == $details->id) selected @endif>{{$details->name}}</option>
                    @endforeach
                  </select>
                </div>

                <div class="col-md-4">
                  <button type="submit" class="btn btn-success">Search</button>
                  
                  <a href="{{ route('job.index') }}">
                    <input type="button" class="btn btn-success" value="Clear">
                  </a>
                </div>
              </div>
            </form>
          </div><br>

          <div class="table-responsive">
            <table class="table align-items-center table-flush">
              <thead class="thead-dark">
                <tr>
                  <th scope="col">id</th>
                  <th scope="col">Job Name</th>
                  <th scope="col">User Name</th>
                  <th scope="col">Mobile</th>
                  <th scope="col">Permit No</th>
                  <th scope="col">Job Type</th>
                  <th scope="col">Created at</th>
                  <th scope="col">Action</th>
                </tr>
              </thead>
               
              <tbody class="list">
                  @if(count($data)> 0)
                    @foreach($data as $key =>$datas)
                      <tr>
                        <!--<td class="budget">{{ ++$key }}</td>-->
						<td class="budget">{{ $datas->id }}</td>
                        <td>{{ $datas->name }}</td>
                        <td>{{ $datas->user->name }}</td>
                        <td>{{ $datas->mobile }}</td>
                        <td>{{ $datas->permit_no }}</td>
                        <td>{{ $datas->job_type }}</td>
                        <td>{{ $datas->created_at->format('F j, Y h:i A') }}</td>

                        <td>
                          <center>
                            <a href="{{ route('job.show', $datas->id) }}" class="">
                              <span class="mr-2"><i class="fa fa-eye" title="View User"></i></span>
                            </a>
                          </center>
                        </td>
                      </tr>
                    @endforeach
                  @else
                    <tr><td colspan="7" class="text-center">No Record Found</td></tr>
                  @endif
              </tbody>
            </table>
          </div>

          <div class="card-footer py-4">
              	 {{ $data->appends(request()->except('page'))->links('vendor.pagination.admin') }}
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection