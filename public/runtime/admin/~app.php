<?php  if (!defined('THINK_PATH')) exit(); filter_request($_REQUEST); filter_request($_GET); filter_request($_POST); define("AUTH_NOT_LOGIN", 1); define("AUTH_NOT_AUTH", 2); function conf($name,$value = false) { if($value === false) { return C($name); } else { if(M("Conf")->where("is_effect=1 and name='".$name."'")->count()>0) { if(in_array($name,array('EXPIRED_TIME','SUBMIT_DELAY','SEND_SPAN','WATER_ALPHA','MAX_IMAGE_SIZE','INDEX_LEFT_STORE','INDEX_LEFT_TUAN','INDEX_LEFT_YOUHUI','INDEX_LEFT_DAIJIN','INDEX_LEFT_EVENT','INDEX_RIGHT_STORE','INDEX_RIGHT_TUAN','INDEX_RIGHT_YOUHUI','INDEX_RIGHT_DAIJIN','INDEX_RIGHT_EVENT','SIDE_DEAL_COUNT','DEAL_PAGE_SIZE','PAGE_SIZE','BATCH_PAGE_SIZE','HELP_CATE_LIMIT','HELP_ITEM_LIMIT','REC_HOT_LIMIT','REC_NEW_LIMIT','REC_BEST_LIMIT','REC_CATE_GOODS_LIMIT','SALE_LIST','INDEX_NOTICE_COUNT','RELATE_GOODS_LIMIT'))) { $value = intval($value); } M("Conf")->where("is_effect=1 and name='".$name."'")->setField("value",$value); } C($name,$value); } } function write_timezone($zone='') { if($zone=='') $zone = conf('TIME_ZONE'); $var = array( '0' => 'UTC', '8' => 'PRC', ); $timezone_config_str = "<?php\r\n"; $timezone_config_str .= "return array(\r\n"; $timezone_config_str.="'DEFAULT_TIMEZONE'=>'".$var[$zone]."',\r\n"; $timezone_config_str.=");\r\n"; $timezone_config_str.="?>"; @file_put_contents(get_real_path()."public/timezone_config.php",$timezone_config_str); } function save_log($msg,$status) { if(conf("ADMIN_LOG")==1) { $adm_session = es_session::get(md5(conf("AUTH_KEY"))); $log_data['log_info'] = $msg; $log_data['log_time'] = NOW_TIME; $log_data['log_admin'] = intval($adm_session['adm_id']); $log_data['log_ip'] = CLIENT_IP; $log_data['log_status'] = $status; $log_data['module'] = MODULE_NAME; $log_data['action'] = ACTION_NAME; M("Log")->add($log_data); } } function get_toogle_status($tag,$id,$field) { if($tag) { return "<span class='is_effect' onclick=\"toogle_status(".$id.",this,'".$field."');\">".l("YES")."</span>"; } else { return "<span class='is_effect' onclick=\"toogle_status(".$id.",this,'".$field."');\">".l("NO")."</span>"; } } function get_is_effect($tag,$id) { if($tag) { return "<span class='is_effect' onclick='set_effect(".$id.",this);'>".l("IS_EFFECT_1")."</span>"; } else { return "<span class='is_effect' onclick='set_effect(".$id.",this);'>".l("IS_EFFECT_0")."</span>"; } } function get_is_verify($tag,$id) { if($tag) { return "<span class='is_effect' onclick='set_verify(".$id.",this);'>".l("IS_EFFECT_1")."</span>"; } else { return "<span class='is_effect' onclick='set_verify(".$id.",this);'>".l("IS_EFFECT_0")."</span>"; } } function get_sort($sort,$id) { if($tag) { return "<span class='sort_span' onclick='set_sort(".$id.",".$sort.",this);'>".$sort."</span>"; } else { return "<span class='sort_span' onclick='set_sort(".$id.",".$sort.",this);'>".$sort."</span>"; } } function get_nav($nav_id) { return M("RoleNav")->where("id=".$nav_id)->getField("name"); } function get_module($module_id) { return M("RoleModule")->where("id=".$module_id)->getField("module"); } function get_group($group_id) { if($group_data = M("RoleGroup")->where("id=".$group_id)->find()) $group_name = $group_data['name']; else $group_name = L("SYSTEM_NODE"); return $group_name; } function get_role_name($role_id) { return M("Role")->where("id=".$role_id)->getField("name"); } function get_admin_name($admin_id) { $adm_name = M("Admin")->where("id=".$admin_id)->getField("adm_name"); if($adm_name) return $adm_name; else return l("NONE_ADMIN_NAME"); } function get_log_status($status) { return l("LOG_STATUS_".$status); } function check_sort($sort) { if(!is_numeric($sort)) { return false; } return true; } function check_empty($data) { if(strim($data)=='') { return false; } return true; } function set_default($null,$adm_id) { $admin_name = M("Admin")->where("id=".$adm_id)->getField("adm_name"); if($admin_name == conf("DEFAULT_ADMIN")) { return "<span style='color:#f30;'>".l("DEFAULT_ADMIN")."</span>"; } else { return "<a href='".u("Admin/set_default",array("id"=>$adm_id))."'>".l("SET_DEFAULT_ADMIN")."</a>"; } } function get_order_sn($order_id) { return M("DealOrder")->where("id=".$order_id)->getField("order_sn"); } function get_order_sn_with_link($order_id) { $order_info = M("DealOrder")->where("id=".$order_id)->find(); if($order_info['type']==0) $str = l("DEAL_ORDER_TYPE_0")."：<a href='".u("DealOrder/deal_index",array("order_sn"=>$order_info['order_sn']))."'>".$order_info['order_sn']."</a>"; else $str = l("DEAL_ORDER_TYPE_1")."：<a href='".u("DealOrder/incharge_index",array("order_sn"=>$order_info['order_sn']))."'>".$order_info['order_sn']."</a>"; if($order_info['is_delete']==1) $str ="<span style='text-decoration:line-through;'>".$str."</span>"; return $str; } function get_user_name($user_id) { $user_name = M("User")->where("id=".$user_id." and is_delete = 0")->getField("user_name"); if(!$user_name) return l("NO_USER"); else return "<a href='".u("User/index",array("user_name"=>$user_name))."'>".$user_name."</a>"; } function get_user_name_js($user_id) { $user_name = M("User")->where("id=".$user_id." and is_delete = 0")->getField("user_name"); if(!$user_name) return l("NO_USER"); else return "<a href='javascript:void(0);' onclick='account(".$user_id.")'>".$user_name."</a>"; } function get_pay_status($status) { return L("PAY_STATUS_".$status); } function get_delivery_status($status,$order_info) { $order_items = unserialize($order_info['deal_order_item']); foreach($order_items as $k=>$v) { $order_item_ids[] = $v['id']; } if(!$order_item_ids) $order_item_ids = 0; else { $order_item_ids = implode(",", $order_item_ids); } $rs = $GLOBALS['db']->getAll("select dn.notice_sn,dn.id from ".DB_PREFIX."delivery_notice as dn where dn.order_item_id in (".$order_item_ids.") "); $result = ""; foreach($rs as $row) { $result .= "".get_notice_info($row['notice_sn'],$row['id'])."<br />"; } return L("ORDER_DELIVERY_STATUS_".$status)."<br />".$result; } function get_order_status($s,$order_info) { if($order_info['extra_status']) $extra_status = l("EXTRA_STATUS_".$order_info['extra_status']); if($order_info['after_sale']) $after_sale = l("AFTER_SALE_".$order_info['after_sale']); if($s) $msg = "订单完结"; else $msg = "待处理"; if($after_sale) $msg.="<br />".$after_sale; if($extra_status) $msg.="<br />".$extra_status; if($order_info['is_delete']==1) $msg.="<br />用户删除"; return "<div style='text-align:center; '>".$msg."</div>"; } function get_order_status_csv($s,$order_info) { if($order_info['extra_status']) $extra_status = l("EXTRA_STATUS_".$order_info['extra_status']); if($order_info['after_sale']) $after_sale = l("AFTER_SALE_".$order_info['after_sale']); if($s) $msg = "订单完结"; else $msg = "待处理"; if($after_sale) $msg.="\n".$after_sale; if($extra_status) $msg.="\n".$extra_status; if($order_info['is_delete']==1) $msg.="\n用户删除"; return $msg; } function get_notice_info($sn,$notice_id) { $express_name = M()->query("select e.name as ename from ".DB_PREFIX."express as e left join ".DB_PREFIX."delivery_notice as dn on dn.express_id = e.id where dn.id = ".$notice_id); $express_name = $express_name[0]['ename']; if($express_name) $str = $express_name."<br/>".$sn; else $str = $sn; return $str; } function get_payment_name($payment_id) { return M("Payment")->where("id=".$payment_id)->getField("name"); } function get_delivery_name($delivery_id) { return M("Delivery")->where("id=".$delivery_id)->getField("name"); } function get_region_name($region_id) { return M("DeliveryRegion")->where("id=".$region_id)->getField("name"); } function get_city_name($id) { return M("DealCity")->where("id=".$id)->getField("name"); } function get_message_is_effect($status) { return $status==1?l("YES"):l("NO"); } function get_message_type($type_name,$rel_id) { $show_name = M("MessageType")->where("type_name='".$type_name."'")->getField("show_name"); if($type_name=='deal_order') { $order_sn = M("DealOrder")->where("id=".$rel_id)->getField("order_sn"); if($order_sn) return "[".$order_sn."] <a href='".u("DealOrder/deal_index",array("id"=>$rel_id))."'>".$show_name."</a>"; else return $show_name; } elseif($type_name=='deal') { $sub_name = M("Deal")->where("id=".$rel_id)->getField("sub_name"); if($sub_name) return "[".$sub_name."]" .$show_name; else return $show_name; } elseif($type_name=='supplier') { $name = M("Supplier")->where("id=".$rel_id)->getField("name"); if($name) return "[".$name."] <a href='".u("Supplier/index",array("id"=>$rel_id))."'>".$show_name."</a>"; else return $show_name; } else { if($show_name) return $show_name; else return $type_name; } } function get_send_status($status) { return L("SEND_STATUS_".$status); } function get_send_mail_type($deal_id) { if($deal_id>0) return l("DEAL_NOTICE"); else return l("COMMON_NOTICE"); } function get_send_type($send_type) { return l("SEND_TYPE_".$send_type); } function get_all_files( $path ) { $list = array(); $dir = @opendir($path); while (false !== ($file = @readdir($dir))) { if($file!='.'&&$file!='..') if( is_dir( $path.$file."/" ) ){ $list = array_merge( $list , get_all_files( $path.$file."/" ) ); } else { $list[] = $path.$file; } } @closedir($dir); return $list; } function get_order_item_name($id) { return M("DealOrderItem")->where("id=".$id)->getField("name"); } function get_supplier_name($id) { return M("Supplier")->where("id=".$id)->getField("name"); } function get_send_type_msg($status) { if($status==0) { return l("SMS_SEND"); } else { return l("MAIL_SEND"); } } function show_content($content,$id) { return "<a title='".l("VIEW")."' href='javascript:void(0);' onclick='show_content(".$id.")'>".l("VIEW")."</a>"; } function get_is_send($is_send) { if($is_send==0) return L("NO"); else return L("YES"); } function get_send_result($result) { if($result==0) { return L("FAILED"); } else { return L("SUCCESS"); } } function get_is_buy($is_buy) { return l("IS_BUY_".$is_buy); } function get_point($point) { return l("MESSAGE_POINT_".$point); } function get_status($status) { if($status) { return l("YES"); } else return l("NO"); } function getMPageName($page) { return L('MPAGE_'.strtoupper($page)); } function getMTypeName($type,$item) { $cfg = $GLOBALS['mobile_cfg']; $navs = null; foreach($cfg as $k=>$v) { if($v['mobile_type']==$item['mobile_type']) { $navs = $v['nav']; break; } } foreach($navs as $k=>$v) { if($v['type']==$type) { return $v['name']; } } } function get_submit_user($uid) { if($uid==0) return "管理员发布"; else { $uname = M("SupplierAccount")->where("id=".$uid)->getField("account_name"); return $uname?$uname:"商家不存在"; } } function get_event_cate_name($id) { return M("EventCate")->where("id=".$id)->getField("name"); } function show_table_substr($word,$cut=20) { return "<span title='".$word."'>".msubstr($word,0,$cut)."</span>"; } function get_balance_status($status) { return l("BALANCE_".$status); } function do_balance($rel_ids,$deal_id,$memo="") { $deal_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal where id = ".$deal_id); $now = NOW_TIME; if($deal_info['is_coupon']==1) { $sql = "update ".DB_PREFIX."deal_coupon set is_balance = 2,balance_time = ".$now.",balance_memo = '".$memo."' where id in (".implode(",",$rel_ids).") and is_balance <> 2"; $sql_amount = "select sum(balance_price)+sum(add_balance_price) from ".DB_PREFIX."deal_coupon where id in (".implode(",",$rel_ids).") and is_balance <> 2"; $amount = $GLOBALS['db']->getOne($sql_amount); $GLOBALS['db']->query($sql); $sql_item = "select doi.* from ".DB_PREFIX."deal_order_item as doi where doi.id in(select distinct(dc.order_deal_id) as item_id from ".DB_PREFIX."deal_coupon as dc where dc.id in (".implode(",",$rel_ids)."))"; $item_list = $GLOBALS['db']->getAll($sql_item); foreach($item_list as $k=>$v) { if($deal_info['deal_type']==1) { $GLOBALS['db']->query("update ".DB_PREFIX."deal_order_item set is_balance = 2,balance_time = ".$now.",balance_memo = '".$memo."' where id = ".$v['id']." and is_balance <> 2"); } else { if($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_coupon where order_deal_id = ".$v['id']." and is_balance = 2")==$v['number']) { $GLOBALS['db']->query("update ".DB_PREFIX."deal_order_item set is_balance = 2,balance_time = ".$now.",balance_memo = '".$memo."' where id = ".$v['id']." and is_balance <> 2"); } else { $GLOBALS['db']->query("update ".DB_PREFIX."deal_order_item set is_balance = 3,balance_time = ".$now.",balance_memo = '".$memo."' where id = ".$v['id']." and is_balance <> 2"); } } } } else { $sql_amount = "select sum(balance_total_price)+sum(add_balance_price_total) from ".DB_PREFIX."deal_order_item where id in (".implode(",",$rel_ids).") and is_balance <> 2"; $amount = $GLOBALS['db']->getOne($sql_amount); $sql = "update ".DB_PREFIX."deal_order_item set is_balance = 2,balance_time = ".$now.",balance_memo = '".$memo."' where id in (".implode(",",$rel_ids).") and is_balance <> 2"; $GLOBALS['db']->query($sql); } supplier_money_log($deal_info['supplier_id'],$amount, $deal_info['sub_name']."结算 ".$memo); } function supplier_money_log($supplier_id,$money,$info) { if($money!=0) { $GLOBALS['db']->query("update ".DB_PREFIX."supplier set money = money +".$money." where id = ".$supplier_id); $log_info['log_info'] = $info; $log_info['create_time'] = NOW_TIME; $log_info['money'] = floatval($money); $log_info['supplier_id'] = $supplier_id; $GLOBALS['db']->autoExecute(DB_PREFIX."supplier_money_log",$log_info); } } function refund_coupon($coupon_id) { return; $coupon_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_coupon where id = ".$coupon_id." and refund_status = 1"); if(!$coupon_data)return 0; $return = 1; $order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".$coupon_data['order_id']); if($coupon_data['coupon_price']<=$order_info['pay_amount']-$order_info['payment_fee']-$order_info['delivery_fee']-$order_info['refund_amound']) { $refund_price = $coupon_data['coupon_price']; } else { $refund_price = $order_info['pay_amount']-$order_info['refund_amound']-$order_info['payment_fee']; $return = 2; } $refund_price-=$coupon_data['coupon_money']; $refund_score = 0-$coupon_data['coupon_score']; if($order_info['pay_status'] == 2) { $deal_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal where id=".intval($coupon_data['deal_id'])); if($deal_info['is_referral'] == 1) { $res = return_referrals($order_info['id']); if($res) { save_log("ID:".$order_info['id'].l("REFERRALS_PAY_SUCCESS"),1); } else { save_log("ID:".$order_info['id'].l("REFERRALS_PAY_FAILED"),0); } } } $GLOBALS['db']->query("update ".DB_PREFIX."deal_coupon set refund_status = 2,is_valid = 2 where id = ".$coupon_data['id']); $GLOBALS['db']->query("update ".DB_PREFIX."deal_order set refund_amount = refund_amount+".$refund_price.",refund_status = 2,after_sale = 1 where id = ".$order_info['id']); $GLOBALS['db']->query("update ".DB_PREFIX."deal set buy_count=buy_count-1 where id=".intval($coupon_data['deal_id'])); $affect_deal = $GLOBALS['db']->getRow("select buy_count from ".DB_PREFIX."deal where id= ".intval($coupon_data['deal_id'])); if($affect_deal['buy_count'] == 0) { $sql_1 = "update ".DB_PREFIX."deal set buy_status = 0 where id= ".intval($coupon_data['deal_id']); } else { $sql_1 = "update ".DB_PREFIX."deal set buy_status = 1 where id= ".intval($coupon_data['deal_id']); } $GLOBALS['db']->query($sql_1); $data = array("money"=>$refund_price,"score"=>$refund_score); require_once APP_ROOT_PATH."system/model/user.php"; modify_account($data,$order_info['user_id'],"来自".$order_info['order_sn']."中团购券".$coupon_data['sn']."的退款 "); order_log("团购券".$coupon_data['sn']."已退款".format_price($refund_price),$order_info['id']); return $return; } function getMobileTypeName($type) { $cfg = $GLOBALS['mobile_cfg']; foreach($cfg as $k=>$v) { if($v['mobile_type']==$type) { return $v['name']; } } } function msubstr_name($n) { return msubstr($n,0,40); } return array ( 'app_debug' => false, 'app_domain_deploy' => false, 'app_plugin_on' => false, 'app_file_case' => false, 'app_group_depr' => '.', 'app_group_list' => '', 'app_autoload_reg' => false, 'app_autoload_path' => 'Think.Util.,@.COM.', 'app_config_list' => array ( 0 => 'taglibs', 1 => 'routes', 2 => 'tags', 3 => 'htmls', 4 => 'modules', 5 => 'actions', ), 'cookie_expire' => 3600, 'cookie_domain' => '', 'cookie_path' => '/', 'cookie_prefix' => '', 'default_app' => '@', 'default_group' => 'Home', 'default_module' => 'Index', 'default_action' => 'index', 'default_charset' => 'utf-8', 'default_timezone' => 'PRC', 'default_ajax_return' => 'JSON', 'default_theme' => 'default', 'default_lang' => 'zh-cn', 'db_type' => 'mysql', 'db_host' => 'localhost', 'db_name' => 'jsd', 'db_user' => 'root', 'db_pwd' => 'macall', 'db_port' => '3306', 'db_prefix' => 'fanwe_', 'db_suffix' => '', 'db_fieldtype_check' => false, 'db_fields_cache' => true, 'db_charset' => 'utf8', 'db_deploy_type' => 0, 'db_rw_separate' => false, 'data_cache_time' => -1, 'data_cache_compress' => false, 'data_cache_check' => false, 'data_cache_type' => 'File', 'data_cache_path' => './admin/../public/runtime/admin/Temp/', 'data_cache_subdir' => false, 'data_path_level' => 1, 'error_message' => '您浏览的页面暂时发生了错误！请稍后再试～', 'error_page' => '', 'html_cache_on' => false, 'html_cache_time' => 60, 'html_read_type' => 0, 'html_file_suffix' => '.shtml', 'lang_switch_on' => false, 'lang_auto_detect' => true, 'log_record' => false, 'log_file_size' => 2097152, 'log_record_level' => array ( 0 => 'EMERG', 1 => 'ALERT', 2 => 'CRIT', 3 => 'ERR', ), 'page_rollpage' => 5, 'page_listrows' => 30, 'session_auto_start' => true, 'show_run_time' => false, 'show_adv_time' => false, 'show_db_times' => false, 'show_cache_times' => false, 'show_use_mem' => false, 'show_page_trace' => false, 'show_error_msg' => true, 'tmpl_engine_type' => 'Think', 'tmpl_detect_theme' => false, 'tmpl_template_suffix' => '.html', 'tmpl_cachfile_suffix' => '.php', 'tmpl_deny_func_list' => 'echo,exit', 'tmpl_parse_string' => '', 'tmpl_l_delim' => '{', 'tmpl_r_delim' => '}', 'tmpl_var_identify' => 'array', 'tmpl_strip_space' => false, 'tmpl_cache_on' => '1', 'tmpl_cache_time' => -1, 'tmpl_action_error' => 'Public:error', 'tmpl_action_success' => 'Public:success', 'tmpl_trace_file' => './admin/ThinkPHP/Tpl/PageTrace.tpl.php', 'tmpl_exception_file' => './admin/ThinkPHP/Tpl/ThinkException.tpl.php', 'tmpl_file_depr' => '/', 'taglib_begin' => '<', 'taglib_end' => '>', 'taglib_load' => true, 'taglib_build_in' => 'cx', 'taglib_pre_load' => '', 'tag_nested_level' => 3, 'tag_extend_parse' => '', 'token_on' => 0, 'token_name' => '__hash__', 'token_type' => 'md5', 'url_case_insensitive' => false, 'url_router_on' => false, 'url_dispatch_on' => true, 'url_model' => '0', 'url_pathinfo_model' => 2, 'url_pathinfo_depr' => '/', 'url_html_suffix' => '', 'var_group' => 'g', 'var_module' => 'm', 'var_action' => 'a', 'var_router' => 'r', 'var_page' => 'p', 'var_template' => 't', 'var_language' => 'l', 'var_ajax_submit' => 'ajax', 'var_pathinfo' => 's', 'default_admin' => 'admin', 'auth_key' => 'fanwe', 'time_zone' => '8', 'admin_log' => '1', 'db_version' => '3.01', 'db_vol_maxsize' => '8000000', 'water_mark' => './public/attachment/201011/4cdde85a27105.gif', 'currency_unit' => '&yen;', 'big_width' => '500', 'big_height' => '500', 'small_width' => '200', 'small_height' => '200', 'water_alpha' => '75', 'water_position' => '4', 'max_image_size' => '3000000', 'allow_image_ext' => 'jpg,gif,png', 'max_file_size' => '1', 'allow_file_ext' => '1', 'bg_color' => '#ffffff', 'is_water_mark' => '1', 'template' => 'fanwe', 'youhui_send_limit' => '5', 'score_unit' => '积分', 'user_verify' => '1', 'shop_logo' => './public/attachment/201011/4cdd501dc023b.png', 'shop_lang' => 'zh-cn', 'shop_title' => '技师达', 'shop_keyword' => '技师达', 'shop_description' => '技师达', 'shop_tel' => '400-800-8888', 'side_deal_count' => '3', 'side_message_count' => '3', 'invite_referrals' => '20', 'invite_referrals_type' => '0', 'online_qq' => '88888888|9999999', 'online_time' => '周一至周六 9:00-18:00', 'deal_page_size' => '12', 'page_size' => '24', 'help_cate_limit' => '4', 'help_item_limit' => '4', 'shop_footer' => '<div style=\\"text-align:center;\\">[方维o2o商业系统] <a target=\\"_blank\\" href=\\"http://www.fanwe.com\\">http://www.fanwe.com</a><br />
</div>
', 'user_message_auto_effect' => '1', 'mail_send_coupon' => '0', 'sms_send_coupon' => '0', 'mail_send_payment' => '0', 'sms_send_payment' => '0', 'reply_address' => 'info@fanwe.com', 'mail_send_delivery' => '0', 'sms_send_delivery' => '0', 'mail_on' => '1', 'sms_on' => '1', 'referral_limit' => '1', 'sms_coupon_limit' => '3', 'mail_coupon_limit' => '3', 'coupon_name' => '技师达券', 'batch_page_size' => '500', 'coupon_print_tpl' => '<div style=\\"border:1px solid #000000;padding:10px;margin:0px auto;width:600px;font-size:14px;\\"><table class=\\"dataEdit\\" cellpadding=\\"0\\" cellspacing=\\"0\\">	<tbody><tr>    <td width=\\"400\\">    	<img src=\\"./public/attachment/201011/4cdd505195d40.gif\\" alt=\\"\\" border=\\"0\\" />     </td>
  <td style=\\"font-weight:bolder;font-size:22px;font-family:verdana;\\" width=\\"43%\\">    序列号：{$bond.sn}<br />
    密码：{$bond.password}    </td>
</tr>
<tr><td colspan=\\"2\\" height=\\"1\\">  <div style=\\"width:100%;border-bottom:1px solid #000000;\\">&nbsp;</div>
  </td>
</tr>
<tr><td colspan=\\"2\\" height=\\"8\\"><br />
</td>
</tr>
<tr><td style=\\"font-weight:bolder;font-size:28px;height:50px;padding:5px;font-family:微软雅黑;\\" colspan=\\"2\\">{$bond.name}</td>
</tr>
<tr><td style=\\"line-height:22px;padding-right:20px;\\" width=\\"400\\">{$bond.user_name}<br />
  生效时间:{$bond.begin_time_format}<br />
  过期时间:{$bond.end_time_format}<br />
  商家电话：<br />
  {$bond.tel}<br />
  商家地址:<br />
  {$bond.address}<br />
  交通路线:<br />
  {$bond.route}<br />
  营业时间：<br />
  {$bond.open_time}<br />
  </td>
  <td><div id=\\"map_canvas\\" style=\\"width:255px;height:255px;\\"></div>
  <br />
  </td>
</tr>
</tbody>
</table>
</div>
', 'public_domain_root' => '', 'show_deal_cate' => '1', 'referral_ip_limit' => '0', 'cart_on' => '1', 'referrals_delay' => '1', 'submit_delay' => '5', 'app_msg_sender_open' => '1', 'admin_msg_sender_open' => '1', 'shop_open' => '1', 'shop_close_html' => '', 'footer_logo' => './public/attachment/201011/4cdd50ed013ec.png', 'gzip_on' => '0', 'integrate_code' => '', 'integrate_cfg' => '', 'shop_seo_title' => '技师达', 'cache_on' => '1', 'expired_time' => '0', 'filter_word' => '', 'style_open' => '0', 'style_default' => '1', 'tmpl_domain_root' => '', 'icp_license' => '', 'count_code' => '', 'deal_msg_lock' => '0', 'promote_msg_lock' => '0', 'list_type' => '1', 'supplier_detail' => '1', 'kuaidi_app_key' => '', 'kuaidi_type' => '2', 'send_span' => '2', 'mail_use_coupon' => '0', 'sms_use_coupon' => '0', 'lottery_sms_verify' => '0', 'lottery_sn_sms' => '0', 'edm_on' => '0', 'edm_username' => '', 'edm_password' => '', 'shop_search_keyword' => '', 'rec_hot_limit' => '4', 'rec_new_limit' => '4', 'baidu_map_appkey' => '', 'rec_cate_goods_limit' => '4', 'sale_list' => '5', 'index_notice_count' => '8', 'relate_goods_limit' => '5', 'user_login_score' => '0', 'user_login_money' => '0', 'user_register_score' => '100', 'user_register_money' => '0', 'domain_root' => '', 'verify_image' => '0', 'tuan_shop_title' => '方维团购', 'mall_shop_title' => '方维商城', 'apns_msg_lock' => '0', 'promote_msg_page' => '0', 'apns_msg_page' => '0', 'store_send_limit' => '5', 'user_login_point' => '10', 'user_register_point' => '100', 'user_login_keep_money' => '0', 'user_login_keep_score' => '5', 'user_login_keep_point' => '50', 'user_active_money' => '0', 'user_active_score' => '0', 'user_active_point' => '10', 'user_active_money_max' => '0', 'user_active_score_max' => '0', 'user_active_point_max' => '100', 'user_delete_money' => '0', 'user_delete_point' => '-10', 'user_add_money' => '0', 'user_add_score' => '0', 'user_add_point' => '10', 'user_delete_score' => '0', 'biz_agreement' => '<ul>                                	<li>                                    &nbsp;&nbsp;&nbsp;&nbsp;您确认，在开始\\"实名认证\\"前，您已详细阅读了本协议所有内容，一旦您开始认证流程，即表示您充分理解并同意接受本协议的全部内容。                                    </li>
                                    <li>                                    &nbsp;&nbsp;&nbsp;&nbsp;为了提高服务的安全性和我们的商户身份的可信度，我们向您提供认证服务。在您申请认证前，您必须先注册成为用户。商户认证成功后，我们将给予每个商户一个认证标识。本公司有权采取各种其认为必要手段对商户的身份进行识别。但是，作为普通的网络服务提供商，本公司所能采取的方法有限，而且在网络上进行商户身份识别也存在一定的困难，因此，本公司对完成认证的商户身份的准确性和绝对真实性不做任何保证。                                    </li>
                                    <li>                                    &nbsp;&nbsp;&nbsp;&nbsp;本公司有权记录并保存您提供给本公司的信息和本公司获取的结果信息，亦有权根据本协议的约定向您或第三方提供您是否通过认证的结论以及您的身份信息。                                         </li>
									<li>										<h3>一、关于认证服务的理解与认同</h3>
										<ol class=\\"decimal\\">											<li>认证服务是由本公司提供的一项身份识别服务。除非本协议另有约定，一旦您的账户完成了认证，相应的身份信息和认证结果将不因任何原因被修改或取消；如果您的身份信息在完成认证后发生了变更，您应向本公司提供相应有权部门出具的凭证，由本公司协助您变更账户的对应认证信息。</li>
											<li>本公司有权单方随时修改或变更本协议内容，并通过网站公告变更后的协议文本，无需单独通知您。本协议进行任何修改或变更后，您还继续使用我们的服务和/或认证服务的，即代表您已阅读、了解并同意接受变更后的协议内容；您如果不同意变更后的协议内容，应立即停用我们的服务和认证服务。</li>
										</ol>
																</li>
<li>										<h3>二、实名认证</h3>
										<ol class=\\"decimal\\">											<li>个体工商户类商户向本公司申请认证服务时，应向本公司提供以下资料：中华人民共和国工商登记机关颁发的个体工商户营业执照或者其他身份证明文件。</li>
											<li>企业类商户向本公司申请认证服务时，应向本公司提供以下资料：中华人民共和国工商登记机关颁发的企业营业执照或者其他身份证明文件。</li>
                                            <li>                                            其他类商户向本公司申请认证服务时，应向本公司提供以下资料：能够证明商户合法身份的证明文件，或者其他本公司认为必要的身份证明文件。                                            </li>
                                            <li>                                            如商户在认证后变更任何身份信息，则应在变更发生后三个工作日内书面通知本公司变更认证，否则本公司有权随时单方终止提供服务，且因此造成的全部后果，由商户自行承担。                                            </li>
                                            <li>                                            通过实名认证的商户不能自行修改已经认证的信息，包括但不限于企业名称、姓名以及身份证件号码等。                                            </li>
										</ol>
									</li>
									<li>										<h3>三、特别声明</h3>
												<ol class=\\"decimal\\">																						<li>认证信息共享：<br />
为了使您享有便捷的服务，您经由其它网站向本公司提交认证申请即表示您同意本公司为您核对所提交的全部认证信息，并同意本公司将是否通过认证的结果及相关认证信息提供给该网站。</li>
											<li>												认证资料的管理：<br />
     您在认证时提交给本公司的认证资料，即不可撤销的授权由本公司保留。本公司承诺除法定或约定的事由外，不公开或编辑或透露您的认证资料及保存在本公司的非公开内容用于商业目的，但本条第1项规定以及以下情形除外：												<ol class=\\"lower-roman\\">													<li>您授权本公司透露的相关信息；</li>
													<li>本公司向国家司法及行政机关提供；</li>
                                                    <li>本公司向本公司关联企业提供；</li>
                                                    <li>第三方和本公司一起为商户提供服务时，该第三方向您提供服务所需的相关信息；</li>
                                                    <li>基于解决您与第三方民事纠纷的需要，本公司有权向该第三方提供您的身份信息。</li>
												</ol>
														</li>
										</ol>
									</li>
																<li>										<h3>四、第三方网站的链接</h3>
                                    </li>
											<li>&nbsp;&nbsp;&nbsp;&nbsp;为实现认证信息审查，我们网站上可能包含了指向第三方网站的链接（以下简称\\"链接网站\\"）。\\"链接网站\\"非由本公司控制，对于任何\\"链接网站\\"的内容，包含但不限于\\"链接网站\\"内含的任何链接，或\\"链接网站\\"的任何改变或更新，本公司均不予负责。自\\"链接网站\\"接收的网络传播或其它形式之传送，本公司不予负责。</li>
									<li>										<h3>五、不得为非法或禁止的使用</h3>
                                    </li>
                                    <li>&nbsp;&nbsp;&nbsp;&nbsp;接受本协议全部的说明、条款、条件是您申请认证的先决条件。您声明并保证，您不得为任何非法或为本协议、条件及须知所禁止之目的进行认证申请。您不得以任何可能损害、使瘫痪、使过度负荷或损害网站或其他网站的服务、或干扰本公司或他人对于认证申请的使用等方式使用认证服务。您不得经由非本公司许可提供的任何方式取得或试图取得任何资料或信息。									</li>
									<li>										<h3>六、有关免责</h3>
                                     </li>
                                     <li>                                     &nbsp;&nbsp;&nbsp;&nbsp;下列情况时本公司无需承担任何责任：                                     </li>
                                     <li>											<ol class=\\"decimal\\">												<li>由于您将账户密码告知他人或未保管好自己的密码或与他人共享账户或任何其他非本公司的过错，导致您的个人资料泄露。</li>
												<li>													任何由于黑客攻击、计算机病毒侵入或发作、电信部门技术调整导致之影响、因政府管制而造成的暂时性关闭、由于第三方原因(包括不可抗力，例如国际出口的主干线路及国际出口电信提供商一方出现故障、火灾、水灾、雷击、地震、洪水、台风、龙卷风、火山爆发、瘟疫和传染病流行、罢工、战争或暴力行为或类似事件等)及其他非因本公司过错而造成的认证信息泄露、丢失、被盗用或被篡改等。															</li>
												<li>由于与本公司链接的其它网站所造成的商户身份信息泄露及由此而导致的任何法律争议和后果。</li>
                                                <li>任何商户向本公司提供错误、不完整、不实信息等造成不能通过认证或遭受任何其他损失，概与本公司无关。</li>
											</ol>
									</li>
																</ul>
', 'index_left_store' => '1', 'index_left_tuan' => '1', 'index_left_youhui' => '1', 'index_left_daijin' => '1', 'index_left_event' => '1', 'index_right_store' => '1', 'index_right_tuan' => '1', 'index_right_youhui' => '1', 'index_right_daijin' => '1', 'index_right_event' => '1', 'user_youhui_down_money' => '0', 'user_youhui_down_score' => '0', 'user_youhui_down_point' => '0', 'apple_path' => 'ios', 'android_path' => 'android', 'qrcode_size' => '5', 'send_score_sms' => '0', 'send_score_mail' => '0', 'youhui_send_tel_limit' => '2', 'ip_limit_num' => '0', 'index_supplier_count' => '8', 'supplier_order_notify' => '1', 'biz_apple_path' => '', 'biz_android_path' => '', 'app_name' => '方维商业系统  fanwe.com ', 'app_sub_ver' => 2390, '_taglibs_' => array ( 'html' => '@.TagLib.TagLibHtml', ), ); ?>