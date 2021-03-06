<?php
/**
 * [WECHAT 2018]
 * [WECHAT  a free software]
 */

defined('IN_IA') or exit('Access Denied');

class AccountTable extends We7Table {

	public function searchAccountList() {
		global $_W;
		$this->query->from('uni_account', 'a')->select('a.uniacid')->leftjoin('account', 'b')
				->on(array('a.uniacid' => 'b.uniacid', 'a.default_acid' => 'b.acid'))
				->where('b.isdeleted !=', '1');

				if (empty($_W['isfounder']) || user_is_vice_founder()) {
			$this->query->leftjoin('uni_account_users', 'c')->on(array('a.uniacid' => 'c.uniacid'))
						->where('a.default_acid !=', '0')->where('c.uid', $_W['uid'])
						->orderby('c.rank', 'desc');
		} else {
			$this->query->where('a.default_acid !=', '0')->orderby('a.rank', 'desc');
		}
		$this->query->orderby('a.uniacid', 'desc');
		$list = $this->query->getall('a.uniacid');
		return $list;
	}

	
	public function userOwnedAccount() {
		global $_W;
		if (!$_W['isfounder']) {
			$users_table = table('users');
			$uniacid_list = $users_table->userOwnedAccount($_W['uid']);
			$this->query->where('u.uniacid', $uniacid_list);
		}
		return $this->query->from('uni_account', 'u')->leftjoin('account', 'a')->on(array('u.default_acid' => 'a.acid'))->where('a.isdeleted', 0)->getall('uniacid');
	}

	public function searchWithKeyword($title) {
		$this->query->where('a.name LIKE', "%{$title}%");
		return $this;
	}

	public function searchWithType($types = array()) {
		$this->query->where(array('b.type' => $types));
		return $this;
	}

	public function searchWithLetter($letter) {
		if (!empty($letter)) {
			$this->query->where('a.title_initial', $letter);
		} else {
			$this->query->where('a.title_initial', '');
		}
		return $this;
	}
}