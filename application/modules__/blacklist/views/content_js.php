<script>
	$(function(){
		

		function shorter(element){

			var string_length	= 100;
			var text 			= element.text();
			var _text 			= element.attr('text');
			var sub_string 		= _text.substring(0,string_length);

			if(_text.length > string_length){
				element.text(sub_string+'...');
			}else{
				parent = element.closest('td');
				$('.show-more',parent).hide();
				$('.show-less',parent).hide();
			}
		}

		$('.show-more a ').on('click',function(e){
			e.preventDefault();
			parent 	= $(this).closest('td');
			text 	= $('.short-text',parent).attr('text');

			$('.short-text',parent).text(text);
			$('.show-more',parent).hide();
			$('.show-less',parent).show();
		});

		$('.show-less a ').on('click',function(e){
			e.preventDefault();
			parent 	= $(this).closest('td');
			shorter($('.short-text',parent));
			$('.show-more',parent).show();
			$('.show-less',parent).hide();
		});

		$('.short-text').each(function(index){
			shorter($(this));
			$('.show-less').hide();
		});
		
		$('.alertApprove').on('click',function(e){
			_this = $(this);
			e.preventDefault();
			alertify.confirm('Apakah anda yakin?',function(evt, val){
				window.location.href = _this.attr('href');
			});
		});
		
	});
	
</script>