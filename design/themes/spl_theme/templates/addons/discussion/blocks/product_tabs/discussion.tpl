{** block-description:discussion_title_product **}

{include
    file="addons/discussion/views/discussion/view.tpl"
    object_id=$product.company_id
    object_type="Addons\\Discussion\\DiscussionObjectTypes::COMPANY"|enum
    locate_to_review_tab=true
    wrap=false
    vendor_discussion=true
    hide_discussion_data=true
    hide_vendor_info=true
}
