<style>
.em-main {
	background-color: #49AF31;
	height: auto;
	padding: 60px 0 60px 0px;
	width: 80%;
	margin: auto;
}
.cofirm-img{width:40%;}
.em-txt p {
	padding: 0;
	margin: 0;
	font-size: 48px;
	font-weight: 600;
	margin-top: 0;
	color: #0870f7;
}
	.em-txt {
		background-color: #fff;
		color: #000;
		font-size: 20px;
		padding: 55px 0 55px 0px;
		text-align: center;
		height: auto;
		width: 80%;
		margin: 0 auto;
	}

	.em-txt h3 
	{
		color: #000;
		font-size: 48px;
		font-weight: 600;
		margin: 0 0 40px;
	}

	.em-txt i 
	{
		color: #333;
		display: block;
		font-size: 100px;
		padding: 0 0 25px;
	}

	@media(min-width:320px) and (max-width:767px) 
	{
		.em-main 
		{
			padding: 18px 18px 0;
		}	

		.em-txt 
		{
			width: 100%;
		}
		.cofirm-img{width:100%;}		
	}
</style>

<div class="em-main">

<div class="em-txt">
	<img src="https://phplaravel-718462-2697156.cloudwaysapps.com/Logo-02.png" class="navbar-brand-img cofirm-img">
	<p>{{$msg}}</p>
	<p>Welcome to See Job Run!</p>
</div>

</div>