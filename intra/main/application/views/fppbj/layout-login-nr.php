<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login NR</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="<?= base_url().'assets/styles/scss/main.min.css' ?>" />
    <link rel="stylesheet" href="../source/vendors/font-awesome/css/all.css" />
    <style>
		.field{
			margin-top: 5px;
			width: calc(100% - 10px * 2);
			border: 1px solid #ddd;
			outline: none;
			border-radius: 2px;
			font-size: 17px;
			color: #121212;
			padding: 5px 10px;
		}
		fieldset{
			border: none;
			margin: none !important;
			padding: none !important;
			margin-inline-start: none !important;
			margin-inline-end: none !important;
			padding-block-start:none !important;
			padding-inline-start: none !important;
			padding-inline-end: none !important;
			padding-block-end: none !important;
		}
    </style>
</head>

<body>
	<div class="bg">
		
	</div>
	
	<canvas id="nokey" width="400" height="400">
     
	</canvas>

	<section class="main-content"> 

	    <div class="wrapper is-centered login-wrapper">
	      <div class="col col-8">
				
				<div class="form-login">
					<div class="group-start">
						<div class="logo-wrapper">
							<img src="<?= base_url().'assets/images/NUSANTARA-REGAS-2.png' ?>" alt="">
						</div>
						<div class="logo-title">
							Aplikasi Kelogistikan

							<div class="line">
								<span></span>
								<span></span>
								<span></span>
							</div>
						</div>
					</div>
					<div class="group-end">
						<div class="field">
							<form action="<?php echo site_url('main/check'); ?>" method="POST">
								<?php echo $message ?>
							<label for="" class="label">Username</label>
							<div class="control">
								<input type="text" class="input" name="username" required>
							</div>

							<label for="" class="label">Password</label>
							<div class="control">
								<input type="Password" class="input" name="password" required>
							</div>

							<div class="control">
								<button class="button is-primary is-expand">Login</button>
							</div>

							<div class="control is-centered">
								<p>atau <a href="#">Daftar Vendor Baru</a></p>
							</div>
						</div>
					</div>
				</div>

	      </div>

	    </div>

	</section>

	<script type="text/javascript" src="<?= base_url().'assets/js/jquery-3.6.3.min.js' ?>"></script>
  	<script type="text/javascript" src="../source/js/app.js"></script>
  	<script>
  		$(document).ready(function() {
  			var canvas = document.getElementById('nokey'),
			   can_w = parseInt(canvas.getAttribute('width')),
			   can_h = parseInt(canvas.getAttribute('height')),
			   ctx = canvas.getContext('2d');

			// console.log(typeof can_w);

			var ball = {
			      x: 0,
			      y: 0,
			      vx: 0,
			      vy: 0,
			      r: 0,
			      alpha: 1,
			      phase: 0
			   },
			   ball_color = {
			       r: 207,
			       g: 255,
			       b: 4
			   },
			   R = 2,
			   balls = [],
			   alpha_f = 0.03,
			   alpha_phase = 0,
			    
			// Line
			   link_line_width = 0.8,
			   dis_limit = 260,
			   add_mouse_point = true,
			   mouse_in = false,
			   mouse_ball = {
			      x: 0,
			      y: 0,
			      vx: 0,
			      vy: 0,
			      r: 0,
			      type: 'mouse'
			   };

			// Random speed
			function getRandomSpeed(pos){
			    var  min = -1,
			       max = 1;
			    switch(pos){
			        case 'top':
			            return [randomNumFrom(min, max), randomNumFrom(0.1, max)];
			            break;
			        case 'right':
			            return [randomNumFrom(min, -0.1), randomNumFrom(min, max)];
			            break;
			        case 'bottom':
			            return [randomNumFrom(min, max), randomNumFrom(min, -0.1)];
			            break;
			        case 'left':
			            return [randomNumFrom(0.1, max), randomNumFrom(min, max)];
			            break;
			        default:
			            return;
			            break;
			    }
			}
			function randomArrayItem(arr){
			    return arr[Math.floor(Math.random() * arr.length)];
			}
			function randomNumFrom(min, max){
			    return Math.random()*(max - min) + min;
			}
			console.log(randomNumFrom(0, 10));
			// Random Ball
			function getRandomBall(){
			    var pos = randomArrayItem(['top', 'right', 'bottom', 'left']);
			    switch(pos){
			        case 'top':
			            return {
			                x: randomSidePos(can_w),
			                y: -R,
			                vx: getRandomSpeed('top')[0],
			                vy: getRandomSpeed('top')[1],
			                r: R,
			                alpha: 1,
			                phase: randomNumFrom(0, 10)
			            }
			            break;
			        case 'right':
			            return {
			                x: can_w + R,
			                y: randomSidePos(can_h),
			                vx: getRandomSpeed('right')[0],
			                vy: getRandomSpeed('right')[1],
			                r: R,
			                alpha: 1,
			                phase: randomNumFrom(0, 10)
			            }
			            break;
			        case 'bottom':
			            return {
			                x: randomSidePos(can_w),
			                y: can_h + R,
			                vx: getRandomSpeed('bottom')[0],
			                vy: getRandomSpeed('bottom')[1],
			                r: R,
			                alpha: 1,
			                phase: randomNumFrom(0, 10)
			            }
			            break;
			        case 'left':
			            return {
			                x: -R,
			                y: randomSidePos(can_h),
			                vx: getRandomSpeed('left')[0],
			                vy: getRandomSpeed('left')[1],
			                r: R,
			                alpha: 1,
			                phase: randomNumFrom(0, 10)
			            }
			            break;
			    }
			}
			function randomSidePos(length){
			    return Math.ceil(Math.random() * length);
			}

			// Draw Ball
			function renderBalls(){
			    Array.prototype.forEach.call(balls, function(b){
			       if(!b.hasOwnProperty('type')){
			           ctx.fillStyle = 'rgba('+ball_color.r+','+ball_color.g+','+ball_color.b+','+b.alpha+')';
			           ctx.beginPath();
			           ctx.arc(b.x, b.y, R, 0, Math.PI*2, true);
			           ctx.closePath();
			           ctx.fill();
			       }
			    });
			}

			// Update balls
			function updateBalls(){
			    var new_balls = [];
			    Array.prototype.forEach.call(balls, function(b){
			        b.x += b.vx;
			        b.y += b.vy;
			        
			        if(b.x > -(50) && b.x < (can_w+50) && b.y > -(50) && b.y < (can_h+50)){
			           new_balls.push(b);
			        }
			        
			        // alpha change
			        b.phase += alpha_f;
			        b.alpha = Math.abs(Math.cos(b.phase));
			        // console.log(b.alpha);
			    });
			    
			    balls = new_balls.slice(0);
			}

			// loop alpha
			function loopAlphaInf(){
			    
			}

			// Draw lines
			function renderLines(){
			    var fraction, alpha;
			    for (var i = 0; i < balls.length; i++) {
			        for (var j = i + 1; j < balls.length; j++) {
			           
			           fraction = getDisOf(balls[i], balls[j]) / dis_limit;
			            
			           if(fraction < 1){
			               alpha = (1 - fraction).toString();

			               ctx.strokeStyle = 'rgba(150,150,150,'+alpha+')';
			               ctx.lineWidth = link_line_width;
			               
			               ctx.beginPath();
			               ctx.moveTo(balls[i].x, balls[i].y);
			               ctx.lineTo(balls[j].x, balls[j].y);
			               ctx.stroke();
			               ctx.closePath();
			           }
			        }
			    }
			}

			// calculate distance between two points
			function getDisOf(b1, b2){
			    var  delta_x = Math.abs(b1.x - b2.x),
			       delta_y = Math.abs(b1.y - b2.y);
			    
			    return Math.sqrt(delta_x*delta_x + delta_y*delta_y);
			}

			// add balls if there a little balls
			function addBallIfy(){
			    if(balls.length < 20){
			        balls.push(getRandomBall());
			    }
			}

			// Render
			function render(){
			    ctx.clearRect(0, 0, can_w, can_h);
			    
			    renderBalls();
			    
			    renderLines();
			    
			    updateBalls();
			    
			    addBallIfy();
			    
			    window.requestAnimationFrame(render);
			}

			// Init Balls
			function initBalls(num){
			    for(var i = 1; i <= num; i++){
			        balls.push({
			            x: randomSidePos(can_w),
			            y: randomSidePos(can_h),
			            vx: getRandomSpeed('top')[0],
			            vy: getRandomSpeed('top')[1],
			            r: R,
			            alpha: 1,
			            phase: randomNumFrom(0, 10)
			        });
			    }
			}
			// Init Canvas
			function initCanvas(){
			    canvas.setAttribute('width', window.innerWidth);
			    canvas.setAttribute('height', window.innerHeight);
			    
			    can_w = parseInt(canvas.getAttribute('width'));
			    can_h = parseInt(canvas.getAttribute('height'));
			}
			window.addEventListener('resize', function(e){
			    console.log('Window Resize...');
			    initCanvas();
			});

			function goMovie(){
			    initCanvas();
			    initBalls(30);
			    window.requestAnimationFrame(render);
			}
			goMovie();

			// Mouse effect
			canvas.addEventListener('mouseenter', function(){
			    console.log('mouseenter');
			    mouse_in = true;
			    balls.push(mouse_ball);
			});
			canvas.addEventListener('mouseleave', function(){
			    console.log('mouseleave');
			    mouse_in = false;
			    var new_balls = [];
			    Array.prototype.forEach.call(balls, function(b){
			        if(!b.hasOwnProperty('type')){
			            new_balls.push(b);
			        }
			    });
			    balls = new_balls.slice(0);
			});
			canvas.addEventListener('mousemove', function(e){
			    var e = e || window.event;
			    mouse_ball.x = e.pageX;
			    mouse_ball.y = e.pageY;
			    // console.log(mouse_ball);
			});

  		})
  	</script>
  	<script>
  		$(document).ready(function() {
  			var lFollowX = 0,
			    lFollowY = 0,
			    x = 0,
			    y = 0,
			    friction = 1 / 30;

			function moveBackground() {
			  x += (lFollowX - x) * friction;
			  y += (lFollowY - y) * friction;
			  
			  translate = 'translate(' + x + 'px, ' + y + 'px) scale(1.1)';

			  $('.bg').css({
			    '-webit-transform': translate,
			    '-moz-transform': translate,
			    'transform': translate
			  });

			  window.requestAnimationFrame(moveBackground);
			}

			$(window).on('mousemove click', function(e) {

			  var lMouseX = Math.max(-100, Math.min(100, $(window).width() / 2 - e.clientX));
			  var lMouseY = Math.max(-100, Math.min(100, $(window).height() / 2 - e.clientY));
			  lFollowX = (20 * lMouseX) / 100; // 100 : 12 = lMouxeX : lFollow
			  lFollowY = (10 * lMouseY) / 100;

			});

			moveBackground();
  		})
  	</script>	
	
</body>
</html>

