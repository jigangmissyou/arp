"use strict";Object.defineProperty(exports,"__esModule",{value:!0}),exports.DEFAULT_SHIPPING_CLASS_OPTIONS=void 0,exports.Edit=Edit;const block_templates_1=require("@woocommerce/block-templates"),components_1=require("@woocommerce/components"),data_1=require("@woocommerce/data"),navigation_1=require("@woocommerce/navigation"),tracks_1=require("@woocommerce/tracks"),components_2=require("@wordpress/components"),compose_1=require("@wordpress/compose"),data_2=require("@wordpress/data"),element_1=require("@wordpress/element"),i18n_1=require("@wordpress/i18n"),core_data_1=require("@wordpress/core-data"),components_3=require("../../../components"),constants_1=require("../../../constants");function mapShippingClassToSelectOption(e){return e.map((({slug:e,name:t})=>({value:e,label:t})))}exports.DEFAULT_SHIPPING_CLASS_OPTIONS=[{value:"",label:(0,i18n_1.__)("No shipping class","woocommerce")},{value:constants_1.ADD_NEW_SHIPPING_CLASS_OPTION_VALUE,label:(0,i18n_1.__)("Add new shipping class","woocommerce")}];const shippingClassRequestQuery={};function extractDefaultShippingClassFromProduct(e,t){const s=null==e?void 0:e.find((({slug:e})=>"uncategorized"!==e));if(s&&!(null==t?void 0:t.some((({slug:e})=>e===s.slug))))return{name:s.name,slug:s.slug}}function Edit({attributes:e,context:{postType:t,isInSelectedTab:s}}){const[o,n]=(0,element_1.useState)(!1),a=(0,block_templates_1.useWooBlockProps)(e),{createProductShippingClass:i}=(0,data_2.useDispatch)(data_1.EXPERIMENTAL_PRODUCT_SHIPPING_CLASSES_STORE_NAME),{createErrorNotice:c}=(0,data_2.useDispatch)("core/notices"),[r]=(0,core_data_1.useEntityProp)("postType",t,"categories"),[l,_]=(0,core_data_1.useEntityProp)("postType",t,"shipping_class"),[p]=(0,core_data_1.useEntityProp)("postType",t,"virtual");function m(e){let t=(0,i18n_1.__)("We couldn’t add this shipping class. Try again in a few seconds.","woocommerce");throw"term_exists"===e.code&&(t=(0,i18n_1.__)("A shipping class with that slug already exists.","woocommerce")),c(t,{explicitDismiss:!0}),e}const{shippingClasses:u}=(0,data_2.useSelect)((e=>{const{getProductShippingClasses:t}=e(data_1.EXPERIMENTAL_PRODUCT_SHIPPING_CLASSES_STORE_NAME);return{shippingClasses:s&&t(shippingClassRequestQuery)||[]}}),[s]),d=(0,compose_1.useInstanceId)(components_2.BaseControl,"wp-block-woocommerce-product-shipping-class-field");return(0,element_1.createElement)("div",{...a},(0,element_1.createElement)("div",{className:"wp-block-columns"},(0,element_1.createElement)("div",{className:"wp-block-column"},(0,element_1.createElement)(components_2.SelectControl,{id:d,name:"shipping_class",value:l,onChange:e=>{e!==constants_1.ADD_NEW_SHIPPING_CLASS_OPTION_VALUE?_(e):n(!0)},label:(0,i18n_1.__)("Shipping class","woocommerce"),options:[...exports.DEFAULT_SHIPPING_CLASS_OPTIONS,...mapShippingClassToSelectOption(null!=u?u:[])],disabled:e.disabled||p,help:(0,element_1.createInterpolateElement)((0,i18n_1.__)("Manage shipping classes and rates in <Link>global settings</Link>.","woocommerce"),{Link:(0,element_1.createElement)(components_1.Link,{href:(0,navigation_1.getNewPath)({tab:"shipping",section:"classes"},"",{},"wc-settings"),target:"_blank",type:"external",onClick:()=>{(0,tracks_1.recordEvent)("product_shipping_global_settings_link_click")}},(0,element_1.createElement)(element_1.Fragment,null))})})),(0,element_1.createElement)("div",{className:"wp-block-column"})),o&&(0,element_1.createElement)(components_3.AddNewShippingClassModal,{shippingClass:extractDefaultShippingClassFromProduct(r,u),onAdd:e=>i(e,{optimisticQueryUpdate:shippingClassRequestQuery}).then((e=>((0,tracks_1.recordEvent)("product_new_shipping_class_modal_add_button_click"),_(e.slug),e))).catch(m),onCancel:()=>n(!1)}))}