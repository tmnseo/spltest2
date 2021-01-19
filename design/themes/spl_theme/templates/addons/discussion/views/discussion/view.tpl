{assign var="discussion" value=$object_id|fn_get_discussion:$object_type:true:$smarty.request}
{if $object_type == "Addons\\Discussion\\DiscussionObjectTypes::ORDER"|enum}
    {$new_post_title = __("new_post")}
{elseif $vendor_discussion}
    {$new_post_title = __("give_feedback")}
{else}
    {$new_post_title = __("write_review")}
{/if}
{if $discussion && $discussion.type != "Addons\\Discussion\\DiscussionTypes::TYPE_DISABLED"|enum}
    <div class="discussion-block" id="{if $container_id}{$container_id}{else}content_discussion{/if}">
        {if $wrap == true}
            {capture name="content"}
            {include file="common/subheader.tpl" title=$title}
        {/if}

        {if $subheader}
            <h4>{$subheader}</h4>
        {/if}

        {if $vendor_discussion}
            {$average_rating = $discussion.average_rating|string_format:"%.1f"}
            {$number_posts = $discussion.posts|count}
            {if !$hide_vendor_info}
                <div class="vendor-discussion__info">
                    {if $average_rating > 0}
                        <div class="vendor-discussion__grade">
                            <span>{$average_rating}</span>
                            <span class="icon-spl-star-like"></span>
                        </div>
                        <div class="vendor-discussion__rating">
                            <strong>
                                {if $average_rating >= 4}
                                    {__("cp_spl.discussion_vendor_tall")}
                                {elseif $average_rating > 2 && $average_rating < 4}
                                    {__("cp_spl.discussion_vendor_middle")}
                                {elseif $average_rating < 2}
                                    {__("cp_spl.discussion_vendor_low")}
                                {/if}
                            </strong>
                            <span>{__("vendor_rating_founded_prefix")} {__("vendor_rating_founded", [$number_posts, "[average_rating]" => $average_rating])}</span>
                        </div>
                    {else}
                        <div class="vendor-discussion__rating">
                            <strong>{__("cp_spl.discussion_vendor_none")}</strong>
                            <span>{__("cp_spl.be_the_first")}</span>
                        </div>
                    {/if}
                    
                    <div class="vendor-discussion__buttons">
                        {if $discussion.type !== "Addons\\Discussion\\DiscussionTypes::TYPE_DISABLED"|enum}
                            {include
                                file="addons/discussion/views/discussion/components/new_post_button.tpl"
                                name=$new_post_title
                                obj_id=$object_id
                                object_type=$discussion.object_type
                                locate_to_review_tab=$locate_to_review_tab
                            }
                        {/if}
                    </div>
                </div>
            {/if}
        {/if}

        <div class="ty-discussion-list__wrap" id="posts_list_{$object_id}">
            {if $discussion.posts}
                {* {include file="common/pagination.tpl" id="pagination_contents_comments_`$object_id`" extra_url="&selected_section=discussion" search=$discussion.search} *}
                 <div class="ty-discussion-list_scroller" id="discussion_list_{$block.block_id}">
                {foreach from=$discussion.posts item=post}
                    {$user_info = $post.user_id|fn_get_user_info}
                    <div class="ty-discussion-post__content">
                        {hook name="discussion:items_list_row"}
                        <div class="ty-discussion-post {cycle values=", ty-discussion-post_even"}" id="post_{$post.post_id}">
                            {if $discussion.type == "Addons\\Discussion\\DiscussionTypes::TYPE_RATING"|enum
                                || $discussion.type == "Addons\\Discussion\\DiscussionTypes::TYPE_COMMUNICATION_AND_RATING"|enum
                                && $post.rating_value > 0
                            }
                                <div class="ty-discussion-post__rating">
                                    {include file="addons/discussion/views/discussion/components/stars.tpl" stars=$post.rating_value|fn_get_discussion_rating}
                                </div>
                            {/if}
                                <span class="ty-discussion-post__author">{$post.name}</span>
                            {if $user_info.company}
                                <span class="ty-discussion-post__author-company">{$user_info.company}</span>
                            {/if}
                            {if !$hide_discussion_data}
                                <span class="ty-discussion-post__date">{$post.timestamp|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}</span>
                            {/if}

                            {if $discussion.type == "Addons\\Discussion\\DiscussionTypes::TYPE_COMMUNICATION"|enum
                                || $discussion.type == "Addons\\Discussion\\DiscussionTypes::TYPE_COMMUNICATION_AND_RATING"|enum
                            }
                                <div class="ty-discussion-post__message">{$post.message|escape|nl2br nofilter}</div>
                            {/if}
                        </div>
                        {/hook}
                    </div>
                {/foreach}
                 </div>
                <div class="owl-theme ty-owl-controls">
                    <div class="owl-controls clickable owl-controls-outside"  id="owl_outside_nav_{$block.block_id}">
                        <div class="owl-buttons">
                            <div id="owl_prev_{$block.block_id}" class="owl-prev"><i class="ty-icon-left-open-thin"></i></div>
                            <div id="owl_next_{$block.block_id}" class="owl-next"><i class="ty-icon-right-open-thin"></i></div>
                        </div>
                    </div>
                </div>

                {* {include file="common/pagination.tpl" id="pagination_contents_comments_`$object_id`" extra_url="&selected_section=discussion" search=$discussion.search} *}
            {else}
                <p class="ty-no-items">{__("no_posts_found")}</p>
            {/if}
        <!--posts_list_{$object_id}--></div>

        {if !$vendor_discussion}
            {if $discussion.type !== "Addons\\Discussion\\DiscussionTypes::TYPE_DISABLED"|enum}
                {include
                    file="addons/discussion/views/discussion/components/new_post_button.tpl"
                    name=$new_post_title
                    obj_id=$object_id
                    object_type=$discussion.object_type
                    locate_to_review_tab=$locate_to_review_tab
                }
            {/if}
        {/if}

        {if $wrap == true}
            {/capture}
            {$smarty.capture.content nofilter}
        {else}
            {capture name="mainbox_title"}{$title}{/capture}
        {/if}
    </div>
{/if}

{include file="addons/discussion/views/discussion/components/scroller_init.tpl" prev_selector="#owl_prev_`$block.block_id`" next_selector="#owl_next_`$block.block_id`"}