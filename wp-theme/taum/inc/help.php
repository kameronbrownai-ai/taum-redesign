<?php
/**
 * In-admin help for editors: a "How to update the site" page and a
 * "Start here" dashboard box. Written for Abby, kept short on purpose.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_menu', function () {
	add_menu_page(
		__( 'How to update the site', 'taum' ),
		__( 'How to update', 'taum' ),
		'edit_posts',
		'taum-help',
		'taum_help_page',
		'dashicons-editor-help',
		4
	);
} );

add_action( 'wp_dashboard_setup', function () {
	if ( current_user_can( 'edit_posts' ) ) {
		wp_add_dashboard_widget( 'taum_start', __( 'Start here', 'taum' ), 'taum_dashboard_widget' );
	}
} );

// Editors see only what they use. The Hostinger plugin menu (with its AI
// content tool) and Comments (closed sitewide) come off for non-admins.
add_action( 'admin_menu', function () {
	if ( ! current_user_can( 'manage_options' ) ) {
		remove_menu_page( 'hostinger' );
		remove_menu_page( 'edit-comments.php' );
		remove_menu_page( 'tools.php' );
	}
}, 999 );

// Editors do not need the default dashboard noise.
add_action( 'wp_dashboard_setup', function () {
	if ( ! current_user_can( 'manage_options' ) ) {
		remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
		remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
		remove_meta_box( 'dashboard_site_health', 'dashboard', 'normal' );
		remove_meta_box( 'dashboard_activity', 'dashboard', 'normal' );
	}
}, 20 );

function taum_dashboard_widget() {
	$a = admin_url();
	?>
	<style>.taum-start a{display:block;padding:10px 12px;margin:6px 0;background:#f6f7f7;border-left:4px solid #2A3060;text-decoration:none;font-weight:600}.taum-start a:hover{background:#eef0f5}</style>
	<div class="taum-start">
	 <a href="<?php echo esc_url( $a . 'post-new.php' ); ?>">📰 <?php esc_html_e( 'Post news', 'taum' ); ?></a>
	 <a href="<?php echo esc_url( $a . 'post-new.php?post_type=taum_event' ); ?>">📅 <?php esc_html_e( 'Add an event', 'taum' ); ?></a>
	 <a href="<?php echo esc_url( $a . 'customize.php?autofocus[panel]=taum_info' ); ?>">🕐 <?php esc_html_e( 'Change meal times, phone, hours, or links', 'taum' ); ?></a>
	 <a href="<?php echo esc_url( $a . 'post-new.php?post_type=taum_partner' ); ?>">🤝 <?php esc_html_e( 'Add a sponsor or partner', 'taum' ); ?></a>
	 <a href="<?php echo esc_url( $a . 'admin.php?page=taum-help' ); ?>">❓ <?php esc_html_e( 'How to update the site (full guide)', 'taum' ); ?></a>
	</div>
	<?php
}

function taum_help_page() {
	$a = admin_url();
	?>
	<style>
	 .taum-help{max-width:760px;font-size:15px;line-height:1.6}
	 .taum-help h1{font-size:26px;margin-bottom:6px}
	 .taum-help h2{font-size:19px;margin:34px 0 8px;padding-top:22px;border-top:1px solid #dcdcde}
	 .taum-help h2:first-of-type{border-top:0;padding-top:0;margin-top:18px}
	 .taum-help ol{margin-left:22px}.taum-help li{margin-bottom:6px}
	 .taum-help .tip{background:#eef6ea;border-left:4px solid #7C8C3F;padding:10px 14px;margin:12px 0}
	 .taum-help .warn{background:#fdf1ec;border-left:4px solid #F0765C;padding:10px 14px;margin:12px 0}
	 .taum-help .go{display:inline-block;margin-top:4px;padding:6px 12px;background:#2A3060;color:#fff;border-radius:4px;text-decoration:none;font-weight:600}
	 .taum-help .go:hover{background:#20264C;color:#fff}
	 .taum-help kbd{background:#f0f0f1;border:1px solid #c3c4c7;border-radius:3px;padding:1px 6px;font-size:13px}
	</style>
	<div class="wrap taum-help">
	 <h1><?php esc_html_e( 'How to update the site', 'taum' ); ?></h1>
	 <p><?php esc_html_e( 'Everything you change here shows on the website right away. You cannot break the design by editing words, so go ahead. If something ever looks wrong, every page keeps its old versions (see the last section).', 'taum' ); ?></p>

	 <h2>📰 <?php esc_html_e( 'Post news', 'taum' ); ?></h2>
	 <ol>
	  <li><?php esc_html_e( 'Go to Posts → Add New.', 'taum' ); ?></li>
	  <li><?php esc_html_e( 'Type a headline, then a few sentences underneath.', 'taum' ); ?></li>
	  <li><?php esc_html_e( 'Optional: on the right, set a Featured image so the post has a photo.', 'taum' ); ?></li>
	  <li><?php esc_html_e( 'Click Publish (top right).', 'taum' ); ?></li>
	 </ol>
	 <div class="tip"><strong><?php esc_html_e( 'To put it on the homepage:', 'taum' ); ?></strong> <?php esc_html_e( 'in the right-hand panel, under Summary, tick "Stick to the top of the blog." The two newest sticky posts appear in "What\'s growing at TAUM." Untick old ones so new ones show.', 'taum' ); ?></div>
	 <a class="go" href="<?php echo esc_url( $a . 'post-new.php' ); ?>"><?php esc_html_e( 'Write a post', 'taum' ); ?></a>

	 <h2>📅 <?php esc_html_e( 'Add an event', 'taum' ); ?></h2>
	 <ol>
	  <li><?php esc_html_e( 'Go to Events → Add event.', 'taum' ); ?></li>
	  <li><?php esc_html_e( 'Type the event name, then a sentence or two about it in the main box.', 'taum' ); ?></li>
	  <li><?php esc_html_e( 'Scroll to "When and where." Fill in the date, time, place, and a sign-up link if there is one.', 'taum' ); ?></li>
	  <li><?php esc_html_e( 'Click Publish.', 'taum' ); ?></li>
	 </ol>
	 <div class="tip"><?php esc_html_e( 'Weekly things like the Monday meal: tick "Repeats every week" and type the day (Mon, Thu). Leave the date blank. One-time events disappear from the site by themselves the day after they happen.', 'taum' ); ?></div>
	 <a class="go" href="<?php echo esc_url( $a . 'post-new.php?post_type=taum_event' ); ?>"><?php esc_html_e( 'Add an event', 'taum' ); ?></a>

	 <h2>🕐 <?php esc_html_e( 'Change a meal time, phone number, hours, or a link', 'taum' ); ?></h2>
	 <p><?php esc_html_e( 'These live in one place and update every page at once.', 'taum' ); ?></p>
	 <ol>
	  <li><?php esc_html_e( 'Click Site Info in the left menu.', 'taum' ); ?></li>
	  <li><?php esc_html_e( 'Open the section: Contact & hours, Meal & grocery times, Forms & links, Homepage hero, Homepage campaign, or Impact numbers.', 'taum' ); ?></li>
	  <li><?php esc_html_e( 'Change the text. The preview on the right updates as you type.', 'taum' ); ?></li>
	  <li><?php esc_html_e( 'Click Publish at the top.', 'taum' ); ?></li>
	 </ol>
	 <div class="tip"><?php esc_html_e( 'When Forty Flames is over: Site Info → Homepage campaign → untick "Show the campaign section." You can reuse that block for the next campaign by changing its text.', 'taum' ); ?></div>
	 <a class="go" href="<?php echo esc_url( $a . 'customize.php?autofocus[panel]=taum_info' ); ?>"><?php esc_html_e( 'Open Site Info', 'taum' ); ?></a>

	 <h2>🤝 <?php esc_html_e( 'Add a sponsor or partner', 'taum' ); ?></h2>
	 <ol>
	  <li><?php esc_html_e( 'Go to Partners → Add partner.', 'taum' ); ?></li>
	  <li><?php esc_html_e( 'Type the name. On the right, tick a Tier: Program partners, or Sponsors & funders.', 'taum' ); ?></li>
	  <li><?php esc_html_e( 'Optional: paste their website in the box below.', 'taum' ); ?></li>
	  <li><?php esc_html_e( 'Click Publish. They appear on the About page.', 'taum' ); ?></li>
	 </ol>
	 <a class="go" href="<?php echo esc_url( $a . 'post-new.php?post_type=taum_partner' ); ?>"><?php esc_html_e( 'Add a partner', 'taum' ); ?></a>

	 <h2>🖼️ <?php esc_html_e( 'Add photos to the gallery', 'taum' ); ?></h2>
	 <ol>
	  <li><?php esc_html_e( 'Go to Pages → Gallery → Edit.', 'taum' ); ?></li>
	  <li><?php esc_html_e( 'Click any photo in the gallery, then the + button in the gallery toolbar, then Upload.', 'taum' ); ?></li>
	  <li><?php esc_html_e( 'Click a photo and type a short caption under it (shows when someone hovers).', 'taum' ); ?></li>
	  <li><?php esc_html_e( 'Click Update.', 'taum' ); ?></li>
	 </ol>
	 <div class="tip"><?php esc_html_e( 'Photos: sideways (landscape) works best. Anything from a phone is fine. If a photo is huge, the site shrinks it for you.', 'taum' ); ?></div>

	 <h2>✏️ <?php esc_html_e( 'Change the words on a page', 'taum' ); ?></h2>
	 <ol>
	  <li><?php esc_html_e( 'Go to Pages, hover the page, click Edit.', 'taum' ); ?></li>
	  <li><?php esc_html_e( 'Click into the text and change it, like a document.', 'taum' ); ?></li>
	  <li><?php esc_html_e( 'The big title and the intro paragraph are on the right: Title at the top, and the "Excerpt" box is the intro under it. The "Featured image" is the big photo.', 'taum' ); ?></li>
	  <li><?php esc_html_e( 'Click Update.', 'taum' ); ?></li>
	 </ol>
	 <div class="warn"><strong><?php esc_html_e( 'One rule:', 'taum' ); ?></strong> <?php esc_html_e( 'change words inside the boxes, don\'t delete whole boxes. The colored cards, callouts, and columns are the design. If you delete one by accident, see the next section.', 'taum' ); ?></div>

	 <h2>🩹 <?php esc_html_e( 'Something looks wrong', 'taum' ); ?></h2>
	 <ol>
	  <li><?php esc_html_e( 'Open the page or post, and in the right-hand panel click "Revisions."', 'taum' ); ?></li>
	  <li><?php esc_html_e( 'Drag the slider back to an earlier version and click "Restore This Revision."', 'taum' ); ?></li>
	  <li><?php esc_html_e( 'Click Update. Done.', 'taum' ); ?></li>
	 </ol>
	 <p><?php esc_html_e( 'Still stuck? Email Kameron with the page name and what you were trying to do. Nothing you can do here is permanent.', 'taum' ); ?></p>
	</div>
	<?php
}
