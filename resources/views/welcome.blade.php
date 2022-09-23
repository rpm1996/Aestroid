<!DOCTYPE html>
<html lang="en">
<head>
  <title>NASA</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.bundle.min.js"></script>
  <script src="//code.jquery.com/jquery-3.2.1.min.js"></script>
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.js"></script>
</head>
<body>
  <div class="container mt-5">
    <div class="row">
      <div class="col-md-3"></div>
      <div class="col-md-6">
        @if (\Session::has('alertMessage'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
          <strong>Alert !</strong> {!! \Session::get('alertMessage') !!}
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
        <form method="post" action="{{url('get-nasa')}}">
          @csrf
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label">From Date</label>
                <input type="date" name="from_date" class="form-control" id="from_date" required value="{{ $fromDate ?? old('from_date') }}">                 
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label">To Date</label>
                <input type="date" name="to_date" class="form-control" id="to_date" required value="{{ $toDate ??  old('to_date') }}">                
              </div> 
            </div>
          </div>         
          <button type="submit" class="btn btn-primary">Submit</button>
        </form>
      </div>
      <div class="col-md-3"></div>
    </div>

    @if(@$getclosestAestroidId)
    <div class="row">
      <div class="col-md-12">
        <div class="container mt-3">           
          <table class="table table-bordered">
            <tbody>
              <tr>
                <td>Fastest Asteroid in km/h</td>
                <td>Asteroid ID - {{$getclosestAestroidId}}</td>
                <td>Speed - {{$getfastestAestroid}}</td>
              </tr>
              <tr>
                <td>Closest Asteroid </td>
                <td>Asteroid ID - {{$getclosestAestroidId}}</td>
                <td>Distance - {{$getclosestAestroid}}</td>
              </tr>
              <tr>
                <td>Average Size of the Asteroids in kilometers</td>
                <td colspan="2">{{ $averageSizeOfAetroid }}</td>
              </tr>
            </tbody>
          </table>
        </div>         
      </div>
      <div class="col-md-12" style="padding: 0px 130px">
        <canvas id="myChart" width="400" height="400"></canvas>
      </div> 
    </div>
    @endif
  </div>
</body>

@if(@$getclosestAestroidId)
<script>
  var noOfAstroids = "{{ json_encode($nasaCountByDateChartValue) }}"; 
  var astroidsAppeardate = "{{ json_encode($nasaCountByDateChartKeys) }}";   

  if(astroidsAppeardate!='')
    astroidsAppeardate = JSON.parse(astroidsAppeardate.replace(/&quot;/g, '"').replace(/&amp;/g, '&'));

  if(noOfAstroids!='')
    noOfAstroids = JSON.parse(noOfAstroids);   
  
  var ctx = document.getElementById('myChart').getContext('2d');
  var myChart = new Chart(ctx, {
      type: 'bar',
      data: {
          labels:astroidsAppeardate, 
          datasets: [
              {
                  label: 'Aestroids Graphs', 
                  data: noOfAstroids, 
                  backgroundColor: [
                      'rgba(255, 99, 132, 0.2)',
                      'rgba(54, 162, 235, 0.2)',
                      'rgba(255, 206, 86, 0.2)',
                      'rgba(75, 192, 192, 0.2)',
                      'rgba(153, 102, 255, 0.2)',
                      'rgba(255, 159, 64, 0.2)',
                      'rgba(61, 253, 13, 0.31)',
                      'rgba(232, 177, 219, 0.37)'
                  ],
                  borderColor: [
                      'rgba(255, 99, 132, 1)',
                      'rgba(54, 162, 235, 1)',
                      'rgba(255, 206, 86, 1)',
                      'rgba(75, 192, 192, 1)',
                      'rgba(153, 102, 255, 1)',
                      'rgba(255, 159, 64, 1)',
                      'rgba(28, 117, 6, 1)',
                      'rgba(169, 27, 136, 1)'
                  ],
                  borderWidth: 1
              }
          ]
      },
      options: {
          scales: {
              yAxes: [{
                  ticks: {
                      beginAtZero: true
                  }
              }]
          }
      }
  });
</script>
@endif
</html>
