<!DOCTYPE html>
<html lang="en">
<head>
    <title>Starter Kit</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="styles/main.min.css" />
    <!-- <link rel="stylesheet" href="assets/css/vendors/jquery-ui.css" /> -->
    <link rel="stylesheet" href="../source/vendors/fontawesome5.6.3/css/all.css" />
</head>

<body>

	<!-- <div class="step">
		<div class="step-Area">
			Lorem
		</div>
		<div class="step-area">
			Ipsum
		</div>
		<div class="btnGroup-wrapper" style="padding: 5px; width: calc(100% - 5px * 2); display: flex; justify-content: flex-end; font-size: 15px;">
			<button class="previous is-primary" style="font-size: 15px;">
				Previous
			</button>
			<button class="next is-primary" style="font-size: 15px;">
				Next
			</button>
		</div>
	</div> -->

	<form id="regForm" action="/action_page.php">
	  <div class="tab" id="tab1">
	    <div class="tab-content">
	    	Hai
	    </div>
		<div class="tab-footer">
	      <button type="button" id="nextBtn2" onclick="next(2)">Next</button>
		</div>
	  </div>
	  <div class="tab" id="tab2">
	    <div class="tab-content">
	    	Hello
	    </div>
		<div class="tab-footer">
		  <button type="button" id="prevBtn1" onclick="prev(1)">Previous</button>
	      <button type="button" id="nextBtn3" onclick="next(3)">Next</button>
		</div>
	  </div>
	  <div class="tab" id="tab3">
	    <div class="tab-content">
	    	d
	    </div>
		<div class="tab-footer">
			<button type="button" id="prevBtn2" onclick="prev(2)">Previous</button>
	      <button type="button" id="submitBtn" onclick="submit()">Submit</button>
		</div>
	  </div>
	</form>

	<script type="text/javascript" src="../source/js/vendors/jquery-3.6.3.min.js">
  	</script>
  	<script>
  		$(document).ready(function() {
  			$('#tab1').css('display','block');

  			$('#nextBtn2').click(function() {
  				$('#tab2').css('display','block');
  				$('#tab1').css('display','none');
  			});

  			$('#nextBtn3').click(function() {
  				$('#tab3').css('display','block');
  				$('#tab2').css('display','none');
  			});

  			$('#prevBtn1').click(function() {
  				$('#tab1').css('display','block');
  				$('#tab2').css('display','none');
  			});

  			$('#prevBtn2').click(function() {
  				$('#tab2').css('display','block');
  				$('#tab3').css('display','none');
  			});
  		})
  	</script>
	
	<script>
		var currentTab = 0; // Current tab is set to be the first tab (0)
		showTab(currentTab); // Display the crurrent tab

		function showTab(n) {
		  // This function will display the specified tab of the form...
		  var x = document.getElementsByClassName("tab");
		  x[n].style.display = "block";
		  //... and fix the Previous/Next buttons:
		  if (n == 0) {
		    document.getElementById("prevBtn").style.display = "none";
		  } else {
		    document.getElementById("prevBtn").style.display = "inline";
		  }
		  if (n == (x.length - 1)) {
		    document.getElementById("nextBtn").innerHTML = "Submit";
		  } else {
		    document.getElementById("nextBtn").innerHTML = "Next";
		  }
		  //... and run a function that will display the correct step indicator:
		  fixStepIndicator(n)
		}

		function nextPrev(n) {
		  // This function will figure out which tab to display
		  var x = document.getElementsByClassName("tab");
		  // Exit the function if any field in the current tab is invalid:
		  if (n == 1 && !validateForm()) return false;
		  // Hide the current tab:
		  x[currentTab].style.display = "none";
		  // Increase or decrease the current tab by 1:
		  currentTab = currentTab + n;
		  // if you have reached the end of the form...
		  if (currentTab >= x.length) {
		    // ... the form gets submitted:
		    document.getElementById("regForm").submit();
		    return false;
		  }
		  // Otherwise, display the correct tab:
		  showTab(currentTab);
		}

		function validateForm() {
		  // This function deals with validation of the form fields
		  var x, y, i, valid = true;
		  x = document.getElementsByClassName("tab");
		  y = x[currentTab].getElementsByTagName("input");
		  // A loop that checks every input field in the current tab:
		  for (i = 0; i < y.length; i++) {
		    // If a field is empty...
		    if (y[i].value == "") {
		      // add an "invalid" class to the field:
		      y[i].className += " invalid";
		      // and set the current valid status to false
		      valid = false;
		    }
		  }
		  // If the valid status is true, mark the step as finished and valid:
		  if (valid) {
		    document.getElementsByClassName("step")[currentTab].className += " finish";
		  }
		  return valid; // return the valid status
		}

		function fixStepIndicator(n) {
		  // This function removes the "active" class of all steps...
		  var i, x = document.getElementsByClassName("step");
		  for (i = 0; i < x.length; i++) {
		    x[i].className = x[i].className.replace(" active", "");
		  }
		  //... and adds the "active" class on the current step:
		  x[n].className += " active";
		}
</script>
</body>

</html>