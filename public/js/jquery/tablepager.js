/*
 * * * * * * * * * * * * * * * * * * * * * *
 *	  s l a s h j q u e r  y . c  o m
 * tablePager v 1.1 - jQuery table widget
 * Copyright (c) 2008 Rasmus Styrk
 *
 * Dual licensed under the MIT and GPL licenses:
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html
 *
 * * * * * * * * * * * * * * * * * * * * * *
*/
 
(function($){
	$.fn.tablePager = function(options) 
	{
		/*	Default values
		*/
		var defaults = {
			offset: 0, //where to start
			limit: 2, // how many rows each page
			placeTop: false // place pager links over the table?
		};
		
		var options = $.extend(defaults, options);
		
		/*	Plugin variables
		*/
		var parent = this;
		var rows = $("tbody > tr", parent);
		var totalItems = rows.length;
		var totalPages = Math.ceil((totalItems / options.limit));
		var currentPage = 1;

		/*	If the total amount of rows is bigger than the defined limit for each page, we do anything;
		*/
		if (totalItems > options.limit)
		{
			/*	Did we have more than 1 page?, if yes, create some page links ;-)
			*/
			var pages = "";
			if(totalPages > 1)
			{
				for(var i = 1; i <= totalPages; i++)
				{
						pages += "<li><a href='javascript://' class='page' id='" + i + "'>" + i + "</a></li>";
				}
			}
			
			/*	HTML Layout for the links
			*/
			var tb = $("<div id='pagerLinks'>\
					   		<ul>\
					   		<li><a href='javascript://' class='previousPage'>Previous</a></li>\
							"+pages+"\
							<li><a href='javascript://' class='nextPage'>Next</a></li>\
							</ul>\
						</div>");

			/*	Appending it to the DOM
			*/
			if(options.placeTop)
			{
				parent.before(tb);
			}
			else
			{
				parent.after(tb);
			}
			
			/*	And then we create functions for the links
			*/
			$(".previousPage").click(
				function()
				{
					options.offset = (options.offset == 0) ? (totalItems-options.limit) : (options.offset-options.limit);
					currentPage = (currentPage == 1) ? totalPages : currentPage-1;
					parent.renderTable();
				}
			);

			$(".nextPage").click(
				function()
				{	
					options.offset = ((options.offset+options.limit) == totalItems) ? 0 : (options.offset+options.limit);
					currentPage = (currentPage == totalPages) ? 1 : currentPage+1;
					parent.renderTable();
				}
			);
			
			$(".page").each
			(
			 	function()
				{
					obj = $(this);
					obj.click(
						function()
						{
							options.offset = (this.id-1)*options.limit;
							currentPage = currentPage-(currentPage-this.id);
							parent.renderTable();
						}
					);
				}
			);
		}
		
		/*	And finally we try to sort out witch rows should be visible ;-)
		*/
		$.fn.renderTable = function ()
		{
			var currentItem = 0;
			
			$("#pagerLinks *").removeClass("active");
			$("#"+currentPage).addClass("active");
			
			/*	So, if we are at last page we hide the next link ;-)
			*/
			if (currentPage == totalPages) 
			{
				$(".nextPage").css("visibility", "hidden");
			} 
			else 
			{
				$(".nextPage").css("visibility", "visible");
			}
					
			/*	If we are at first page we hide the prev link
			*/
			if (currentPage == 1) 
			{
				$(".previousPage").css("visibility", "hidden");
			} 
			else 
			{
				$(".previousPage").css("visibility", "visible");
			}			
			
			/*	Run through all rows and check what we want to show
			*/
			return rows.each
			(
				function()
				{
					obj = $(this);
	
					if (currentItem >= options.offset && currentItem < (options.offset+options.limit))
					{
						obj.show();	
					}
					else
					{
						obj.hide();	
					}
					
					currentItem++;
				}
			);
		}
		
		/*	Initialize on page load
		*/
		this.renderTable();
  };  
})(jQuery);  