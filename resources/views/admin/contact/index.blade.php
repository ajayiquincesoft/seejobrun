@extends('layouts.app')

@section('content')
  <div class="header pb-6">
    <div class="container-fluid">
      <div class="header-body">
        <div class="row align-items-center py-4">
          <div class="col-lg-6 col-7">
            <h6 class="h2 text-white d-inline-block mb-0">Contact Information</h6>
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
          <div class="table-responsive">
            <table class="table align-items-center  table-flush">
              <thead class="thead-dark">
                <tr>
                  <th scope="col">id</th>
                  <th scope="col">Name</th>
                  <th scope="col">Mobile</th>
                  <th scope="col">Email</th>
                  <th scope="col">User Type</th>
                  <th scope="col">Created at</th>
                  <th scope="col">Action</th>
                </tr>
              </thead>
               
              <tbody class="list">
                  @if(count($data)> 0)
                    @foreach($data as $key =>$datas)
                      <tr>
                        <td class="budget">{{ ++$key }}</td>
                        <td>{{ $datas->name }}</td>
                        <td>{{ $datas->mobile }}</td>
                        <td>{{ $datas->email }}</td>
                        <td>{{ ($datas->type==1)?'Client':(($datas->type==2)?'Sub Contractor':(($datas->type==3)?'Employee':(($datas->type==4)?'General Contractor':(($datas->type==5)?'Architect/Engineer':(($datas->type==6)?'Interior Designer':(($datas->type==7)?'Inspector':'Bookkeeper')))))) }}</td>
                        <td>{{ $datas->created_at->format('d M Y h:i A') }}</td>

                        <td>
                          <center>
                            <a href="{{ route('contact.show', $datas->id) }}" class="text-white">
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
              
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection