(function() {

	tinymce.create('tinymce.plugins.sympleShortcodeMce', {
		init : function(ed, url){
			tinymce.plugins.sympleShortcodeMce.theurl = url;
		},

		createControl : function(btn, e) {
			if ( btn == "wpo_shortcodes_button" ) {
				var a = this;
				var btn = e.createSplitButton('symple_button', {
	                title: "Insert Shortcode",
					image: tinymce.plugins.sympleShortcodeMce.theurl +"/images/shortcodes.png",
					icons: false,
	            });
	            btn.onRenderMenu.add(function (c, b) {
					b.add({title : 'Symple Shortcodes', 'class' : 'mceMenuItemTitle'}).setDisabled(1);
					// Columns
					c = b.addMenu({title:"Columns"});
						a.render( c, "Row", "row" );
						c.addSeparator();
						a.render( c, "One Full", "one_full" );
						a.render( c, "One Half", "one_half" );
						a.render( c, "One Third", "one_third" );
						a.render( c, "Two Third", "two_third" );
						a.render( c, "One Fourth", "one_fourth" );
					b.addSeparator();
				});
	          return btn;
			}
			return null;
		},

		render : function(ed, title, id) {
			ed.add({
				title: title,
				onclick: function () {
					// Selected content
					var mceSelected = tinyMCE.activeEditor.selection.getContent();
					// Add highlighted content inside the shortcode when possible - yay!
					if ( mceSelected ) {
						var sympleDummyContent = mceSelected;
					} else {
						var sympleDummyContent = 'Sample Content';
					}
					// Accordion
					if(id == "accordion") {
						tinyMCE.activeEditor.selection.setContent('[accordion id="accordion_id"]<br>[accordion_item animate="" id_parent="accordion_id" title="Collapsible Group Item #1"]<br>Accordion content<br>[/accordion_item]<br>[accordion_item animate="" id_parent="accordion_id" title="Collapsible Group Item #2"]<br>Accordion content<br>[/accordion_item]<br>[accordion_item animate="" id_parent="accordion_id" title="Collapsible Group Item #3"]<br>Accordion content<br>[/accordion_item]<br>[/accordion]');
					}
					// Person
					if(id == "person1") {
						tinyMCE.activeEditor.selection.setContent('[person name="Jonh Doe" link="#" picture="http://demo.shinetheme.com/wpcne/wp-content/uploads/2013/08/member1.jpg" title="President &amp; Founder" twitter="#" facebook="#" linkedin="#" gplus="#"]Diam amet, dolor a et duis ultricies cum ut ac enim nec sit. Etiam scelerisque aenean [/person]');
					}
					if(id == "person2") {
						tinyMCE.activeEditor.selection.setContent('[person2 picture="http://demo.shinetheme.com/wpcne/wp-content/uploads/2013/09/person21.jpg" picture_position="left" name="Jonh Doe" title="President &amp; Founder" twitter="#" facebook="#" linkedin="#" gplus="#"]<br/>Ultrices rhoncus rhoncus. Tristique rhoncus! Ac augue, in nec, ut porta ac urna tristique mauris eros velit platea arcu. <a href="#">Tortor aliquet mattis lorem!</a> Eros odio.<br/>[/person2]');
					}
					// Button
					if(id == "button") {
						tinyMCE.activeEditor.selection.setContent('[button link="#" size="" type="danger"]Button[/button]');
					}
					// Clear Floats
					if(id == "line") {
						tinyMCE.activeEditor.selection.setContent('[line]');
					}
					// Dropcap
					if(id == "line") {
						tinyMCE.activeEditor.selection.setContent('[dropcap][/dropcap]');
					}
					// Callout
					if(id == "callout") {
						tinyMCE.activeEditor.selection.setContent('[purchase btn_position="right" title="The title" button_text="button text" icon="" link="http://www.shinetheme.com" button_rel="nofollow"]' + sympleDummyContent + '[/purchase]');
					}
					// Columns
					if(id == "row") {
						tinyMCE.activeEditor.selection.setContent('[row_fluid class=""]<br />' + sympleDummyContent + '<br />[/row_fluid]');
					}
					if(id == "half") {
						tinyMCE.activeEditor.selection.setContent('[col_6 class="" title=""]<br />' + sympleDummyContent + '<br />[/col_6]');
					}

					if(id == "third") {
						tinyMCE.activeEditor.selection.setContent('[col_4 class="" title=""]' + sympleDummyContent + '[/col_4]');
					}
					if(id == "fourth") {
						tinyMCE.activeEditor.selection.setContent('[col_3 class="" title=""]' + sympleDummyContent + '[/col_3]');
					}
					if(id == "sixth") {
						tinyMCE.activeEditor.selection.setContent('[col_2 class="" title=""]' + sympleDummyContent + '[/col_2]');
					}
					if(id == "one_full") {
						tinyMCE.activeEditor.selection.setContent('[one_full animate="" class=""]' + sympleDummyContent + '[/one_full]');
					}
					if(id == "one_half") {
						tinyMCE.activeEditor.selection.setContent('[one_half animate="" class=""]' + sympleDummyContent + '[/one_half]');
					}
					if(id == "one_third") {
						tinyMCE.activeEditor.selection.setContent('[one_third animate="" class=""]' + sympleDummyContent + '[/one_third]');
					}
					if(id == "two_third") {
						tinyMCE.activeEditor.selection.setContent('[two_third animate="" class=""]' + sympleDummyContent + '[/two_third]');
					}
					if(id == "one_fourth") {
						tinyMCE.activeEditor.selection.setContent('[one_fourth animate="" class=""]' + sympleDummyContent + '[/one_fourth]');
					}
					// Google Map
					if(id == "googlemap") {
						tinyMCE.activeEditor.selection.setContent('[gmap address="New York" html="My company addres here"]');
					}
					// Heading
					if(id == "heading") {
						tinyMCE.activeEditor.selection.setContent('[symple_heading type="h2" title="' + sympleDummyContent + '" margin_top="20px;" margin_bottom="20px" text_align="left"]');
					}
					// Pricing
					if(id == "pricing") {
						tinyMCE.activeEditor.selection.setContent('[pricing_table title="Basic" price="$10" time="per year" button_link="#" button_text="Sign Up"]<br>[pricing_row]150 Mb Storage[/pricing_row]<br/>[pricing_row]1 Domain[/pricing_row]<br>[pricing_row]2 Sub Domains[/pricing_row]<br/>[pricing_row]5 MySQL DBs[/pricing_row]<br/>[pricing_row]1 Email[/pricing_row]<br/>[/pricing_table]');
					}
					//Spacing
					if(id == "spacing") {
						tinyMCE.activeEditor.selection.setContent('[space]');
					}
					//Social
					if(id == "social") {
						tinyMCE.activeEditor.selection.setContent('[socials tt_background="#e1e1e1" hover_color="#fff" hover_background="#de5cc8" background="#f3f3f3"]<br/>[social_icon text="Facebook" url="#facebook" icon="facebook"]<br/>[social_icon text="Google+" url="#google+" icon="googleplus-2"]<br/>[social_icon text="Twitter" url="#twitter" icon="twitter"]<br/>[social_icon text="Linkedin" url="#linkedin" icon="linkedin-2"]</br/>[/socials]');
					}
					//Skillbar
					if(id == "skillbar") {
						tinyMCE.activeEditor.selection.setContent('[skills]<br/>[skill skill_name="Graphic Design" level="85"][/skill]<br/>[skill skill_name="Web Design" level="100"][/skill]<br/>[skill skill_name="Web Development" level="65"][/skill]<br/>[skill skill_name="Mobile Optimized" level="45"][/skill]<br/>[/skills]<br/>');
					}
					//Tabs
					if(id == "tabs") {
						tinyMCE.activeEditor.selection.setContent('[tabs tab1="Home" tab2="Profile" tab3="About"]<br/>[tab id="tab1"]<br>Tab content<br>[/tab]<br/>[tab id="tab2"]<br>Tab content<br/>[/tab]<br/>[tab id="tab3"]<br>Tab content<br/>[/tab]<br>[/tabs]');
					}
					//Testimonial
					if(id == "testimonial") {
						tinyMCE.activeEditor.selection.setContent('[testimonials title="Client Testimonials" layout="block" items="3"]<br/>[testimonial avatar="http://demo.shinetheme.com/wpcne/wp-content/uploads/2013/08/avatar1.jpg" class="" name="David" job="developer"]Pulvinar cursus pulvinar duis placerat! Dis cursus vel augue velit, cum urna turpis, urna, pid urna mus integer vel, in, honcus mattis, nisi sociis, natoque augue 1.[/testimonial]<br/>[testimonial avatar="http://demo.shinetheme.com/wpcne/wp-content/uploads/2013/08/avatar2.jpg" class="" name="Rita" job="SEOer"]Pulvinar cursus pulvinar duis placerat! Dis cursus vel augue velit, cum urna turpis, urna, pid urna mus integer vel, in, honcus mattis, nisi sociis, natoque augue 2.[/testimonial]<br/>[testimonial avatar="http://demo.shinetheme.com/wpcne/wp-content/uploads/2013/08/avatar1.jpg" class="" name="David" job="developer"]Pulvinar cursus pulvinar duis placerat! Dis cursus vel augue velit, cum urna turpis, urna, pid urna mus integer vel, in, honcus mattis, nisi sociis, natoque augue 1.[/testimonial]<br/>[testimonial avatar="http://demo.shinetheme.com/wpcne/wp-content/uploads/2013/08/avatar2.jpg" class="" name="Rita" job="SEOer"]Pulvinar cursus pulvinar duis placerat! Dis cursus vel augue velit, cum urna turpis, urna, pid urna mus integer vel, in, honcus mattis, nisi sociis, natoque augue 2.[/testimonial]<br/>[/testimonials]');
					}
					if(id == "testimonial_bx") {
						tinyMCE.activeEditor.selection.setContent('[testimonials_bx ]<br/>[testimonial_bx avatar="http://demo.shinetheme.com/wpcne/wp-content/uploads/2013/08/avatar1.jpg?timestamp=1378198117250" name="Tom"]<br/>Et a urna integer, duis pulvinar montes eros montes elementum sit tristique enim odio arcu sed natoque tincidunt, diam, magnis phasellus diam cras magnis. Integer in porttitor cum. lacus platea, nisi mattis, a porta, turpis eu integer elementum magnis tincidunt, augue elementum etiam, sed nisi vut, porttitor mattis.<br/>[/testimonial_bx]<br/>[testimonial_bx avatar="http://demo.shinetheme.com/wpcne/wp-content/uploads/2013/08/avatar2.jpg?timestamp=1378199312691" name="Rita"]<br/>Et a urna integer, duis pulvinar montes eros montes elementum sit tristique enim odio arcu sed natoque tincidunt, diam, magnis phasellus diam cras magnis. Integer in porttitor cum. lacus platea, nisi mattis, a porta, turpis eu integer elementum magnis tincidunt, augue elementum etiam, sed nisi vut, porttitor mattis.<br/>[/testimonial_bx]<br/>[/testimonials_bx]');
					}
					if(id == "testimonial_rotator") {
						tinyMCE.activeEditor.selection.setContent('[testimonials_rotator]<br/>[rotator_content avatar="http://demo.shinetheme.com/wpcne/wp-content/uploads/2013/08/member2.jpg" name="Rita"]<br/>Nothing will benefit human health and increase the chances for survival of life on Earth as much as the evolution to a vegetarian diet.<br/>[/rotator_content]<br/>[rotator_content avatar="http://demo.shinetheme.com/wpcne/wp-content/uploads/2013/08/member3.jpg" name="David"]<br/>If you don\'t want to be beaten, imprisoned, mutilated, killed or tortured then you shouldn\'t condone such behaviour towards anyone, be they human or not.<br/>[/rotator_content]</br>[/testimonials_rotator]');
					}
					if(id == "alert") {
						tinyMCE.activeEditor.selection.setContent('[alert type="success"]Success! Elementum magna quis facilisis dictumst, tincidunt lacus nunc [/alert]');
					}
					if(id == "recent_posts") {
						tinyMCE.activeEditor.selection.setContent('[recent_post title="Recent Posts 4 items" count="5" id="2"]');
					}
					if(id == "boxes") {
						tinyMCE.activeEditor.selection.setContent('[box style="1" align="center" col="3" icon="icon-gift-2" title="free google fonts"]<br/>Box example content.<br/>[/box]<br/>');
					}
					if(id == "contact_box") {
						tinyMCE.activeEditor.selection.setContent('[contact_box col="4" title="Give a call" icon="icon-phone" address1="31-33 Randall St" phone1="+84 123 456 789" address2="165-177 Main St" phone2="+84 123 888 888"][/contact_box]');
					}
					if(id == "service_box") {
						tinyMCE.activeEditor.selection.setContent('[service_box col="3" picture="http://demo.shinetheme.com/wpcne/wp-content/uploads/2013/09/service1.jpg" icon="icon-install" title="Free install"]Et ut eros! Facilisis magna mattis nisi lorem turpis ut in aenean. Adipiscing dictumst! [/service_box]');
					}
					//Toggle
					if(id == "toggle") {
						tinyMCE.activeEditor.selection.setContent('[symple_toggle title="This Is Your Toggle Title"]' + sympleDummyContent + '[/symple_toggle]');
					}
					return false;
				}
			})
		}
	});
	tinymce.PluginManager.add("symple_shortcodes", tinymce.plugins.sympleShortcodeMce);
})();