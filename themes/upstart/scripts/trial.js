$( document ).ready(function() {
  // When location is changed update corresponding class offerings
  $('#new_trial_location_id').change(function() {
    $('#new_trial_offering_id').html("<option disabled selected> &mdash; select an option &mdash; </option>")
    $.ajax({
      type:'get',
      url: 'http://app.mathplusacademy.com/offerings/offerings_by_location.json',
      data: {location_id: $(this).val()},
      success: function(data, status, xhr) {
        var courses = data;
        $.each(courses, function(index, value) {
          $('#new_trial_offering_id').append($("<option></option>").attr("value", value[0]).attr("data-day", value[2]).text(value[1]));
        });
      },
      error: function (xhr, status, e) {
      },
      dataType: 'JSON'
    });
  })

  $('#new_trial_offering_id').change(function() {
    $('#new_trial_trial_date').prop('disabled', false) //remove disabled from trial date
    var selected = $(this).find('option:selected');
    var day = selected.data('day');
    // alert(day) //Use day to scope dates user can select from with date picker
    var days_of_the_week = ['Sunday','Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']
    var day_index = days_of_the_week.indexOf(day)
    // add date picker to trial date field
    var dateToday = new Date();
    $("#new_trial_trial_date").datepicker("destroy");
    $('#new_trial_trial_date').datepicker({
          dateFormat : 'dd-mm-yy',
		      minDate: dateToday,
          beforeShowDay: function(day) {
            var day = day.getDay();
              return [day == day_index , ""];
          }
      });
  })

  // Populate location select with available locations
  if (document.getElementById('new_trial')) {
    $.ajax({
      type:'get',
      url: 'http://app.mathplusacademy.com/locations/list.json',
      success: function(data, status, xhr) {
        var locations = data;
        $.each(locations, function(index, value) {
          $('#new_trial_location_id').append($("<option></option>").attr("value", value[0]).text(value[1]));
        });
      },
      error: function (xhr, status, e) {
      },
      dataType: 'JSON'
    });
  } else {

  }

  $('#new_trial_trial_date').on('change', function() {
    if ($('#new_trial_trial_date').val()) {
      $('#new_trial').find('button[name="register-button"]').removeAttr('disabled');
    }
  });

  $('#new_trial').submit(function() {
    var $submitButton = $(this).find('button[name="register-button"]');
    $submitButton.attr('data-origText', $submitButton.val());
    $submitButton.val("Submitting...");
    if($('#new_trial_location_id').val() == null){
      alert('Please ensure all fields are filled. You may have to enable JavaScript or try using a different browser like Chrome or Safari.')
      return false;
    }
  });
});
