<div class="row-white pad-y-5">
	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-8 col-sm-offset-2">
				<h2 class="mar-0 mar-b-4 text-9"><?php esc_html_e( $page->post_title ); ?></h2>
				<?php echo wpautop( $content ); ?>
				<div class="row mar-y-3">
					<div class="col-sm-5">
						<a class="btn btn-primary btn-block btn-shadow text-4 mar-b-2" href="<?php echo home_url(); ?>/rasds/login/">
							<b class="text-5">Login</b><br>
							<span class="btn-italic">Already Registered?</span>
						</a>
					</div>
					<div class="col-sm-5">
						<a class="btn btn-primary btn-block btn-shadow text-4" href="<?php echo home_url(); ?>/rasds/register/">
							<b class="text-5">Register</b><br>
							<span class="btn-italic">New User?</span>
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


