<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
	<title><?php echo (!empty($pageTitle))? $pageTitle . ' | ' : ''; ?>HeroLinks</title>

	<!-- Le HTML5 shim, for IE6-8 support of HTML elements -->
	    <!--[if lt IE 9]>
	      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	 <![endif]-->
	
	<link rel="stylesheet" href="css/bootstrap.min.css">
	
    <style type="text/css">
      /* Override some defaults */
      html, body {
        background-color: #eee;
      }
      body {
        padding-top: 40px; /* 40px to make the container go all the way to the bottom of the topbar */
      }
      .container > footer p {
        text-align: center; /* center align it with the container */
      }
      .container {
        width: 820px; /* downsize our container to make the content feel a bit tighter and more cohesive. NOTE: this removes two full columns from the grid, meaning you only go to 14 columns and not 16. */
      }

      /* The white background content wrapper */
      .content {
        background-color: #fff;
        padding: 20px;
        margin: 0 -20px; /* negative indent the amount of the padding to maintain the grid system */
        -webkit-border-radius: 0 0 6px 6px;
           -moz-border-radius: 0 0 6px 6px;
                border-radius: 0 0 6px 6px;
        -webkit-box-shadow: 0 1px 2px rgba(0,0,0,.15);
           -moz-box-shadow: 0 1px 2px rgba(0,0,0,.15);
                box-shadow: 0 1px 2px rgba(0,0,0,.15);
      }

      /* Page header tweaks */
      .page-header {
        background-color: #f5f5f5;
        padding: 20px 20px 10px;
        margin: -20px -20px 20px;
      }

      /* Styles you shouldn't keep as they are for displaying this base example only */
      .content .span10,
      .content .span4 {
        min-height: 500px;
      }
      /* Give a quick and non-cross-browser friendly divider */
      .content .span4 {
        margin-left: 0;
        padding-left: 19px;
        border-left: 1px solid #eee;
      }

      .topbar .btn {
        border: 0;
      }
		.input .fb-profile {
			line-height: 2.3em;
			background: transparent url(../img/facebook_icon.gif) no-repeat center left;
			padding-left: 20px;
		}
    </style>

</head>

<body>

	<div class="topbar">
		<div class="fill">
			<div class="container">

				<a class="brand" href="/">Hero Links</a>

				<ul class="nav">
					<li<?php if ('home' == $action) echo ' class="active"'; ?>><a href="/">Home</a></li>
					<li<?php if ('new' == $action) echo ' class="active"'; ?>><a href="/new">Add link</a></li>
				</ul>

				<form action="/search" method="get" class="pull-right">
					<input type="text" name="key" placeholder="Search" <?php if (!empty($key)) printf('value="%s"', $key); ?>>
				</form>

			</div>
		</div><!-- /fill -->
	</div><!-- /topbar -->

	<div class="container">
		
		<div class="content">
	