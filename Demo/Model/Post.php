<?php

class Model_Post extends PhalApi_Model_NotORM {

    public function getIndexPost($page) {

        $num=6;
        $rs   = array();
        $sql = 'SELECT pb.id AS postID,pb.title,pd.text,pd.createTime,ub.nickName,gb.name AS groupName '
             . 'FROM post_detail pd,post_base pb ,group_base gb,user_base ub '
             . 'WHERE pb.id=pd.post_base_id AND pb.user_base_id=ub.id AND pb.group_base_id=gb.id '
             . 'GROUP BY pb.id '
             . 'ORDER BY MAX(pd.createTime) DESC '
             . 'LIMIT :start,:num ';
        $params = array(':start' =>($page-1)*$num , ':num' =>$num);
        $rs['posts'] = DI()->notorm->user_base->queryAll($sql, $params);

        $sql = 'SELECT ceil(count(*)/:num) AS pageCount '
             . 'FROM post_base ';

        $params = array(':num' =>$num);
        $pageCount = DI()->notorm->user_base->queryAll($sql, $params);
        $rs['pageCount'] = (int)$pageCount[0]['pageCount'];
        $rs['currentPage'] = $page;
        return $rs;
    }

    public function getGroupPost($groupID,$page) {

        $num=6;
        $rs   = array();
        $sql = 'SELECT  pb.id AS postID,pb.title,pd.text,pd.createTime,ub.nickName,gb.name AS groupName '
             . 'FROM post_detail pd,post_base pb ,group_base gb,user_base ub '
             . 'WHERE pb.id=pd.post_base_id AND pb.user_base_id=ub.id AND pb.group_base_id=gb.id AND pb.group_base_id=:group_id '
             . 'GROUP BY pb.id '
             . 'ORDER BY MAX(pd.createTime) DESC '
             . 'LIMIT :start,:num ';
        $params = array(':group_id' =>$groupID,':start' =>($page-1)*$num , ':num' =>$num);
        $rs['posts'] = DI()->notorm->post_base->queryAll($sql, $params);

        $sql = 'SELECT ceil(count(*)/:num) AS pageCount '
             . 'FROM post_base pb,group_base gb '
             . 'WHERE pb.id=gb.id AND gb.id=:group_id ';

        $params = array(':group_id' =>$groupID,':num' =>$num);
        $pageCount = DI()->notorm->user_base->queryAll($sql, $params);
        $rs['pageCount'] = (int)$pageCount[0]['pageCount'];
        $rs['currentPage'] = $page;
        return $rs;
    }

    public function getMyGroupPost($userID,$page) {

        $num=6;
        $rs   = array();
        $sql = 'SELECT  pb.id AS postID,pb.title,pd.text,pd.createTime,ub.nickName,gb.name AS groupName '
             . 'FROM post_detail pd,post_base pb ,group_base gb,user_base ub '
             . 'WHERE pb.id=pd.post_base_id AND pb.user_base_id=ub.id AND pb.group_base_id=gb.id '
             . 'AND gb.id in (SELECT group_base_id FROM group_detail gd WHERE gd.user_base_id =:user_id )'
             . 'GROUP BY pb.id '
             . 'ORDER BY MAX(pd.createTime) DESC '
              . 'LIMIT :start,:num ';
        $params = array(':user_id' =>$userID,':start' =>($page-1)*$num , ':num' =>$num );
        $rs['posts'] = DI()->notorm->post_base->queryAll($sql, $params);

        $sql = 'SELECT ceil(count(*)/:num) AS pageCount '
             . 'FROM post_base pb,group_base gb,group_detail gd '
             . 'WHERE pb.id=gb.id AND gb.id=gd.group_base_id AND gd.user_base_id=:user_id ';

        $params = array(':user_id' =>$userID,':num' =>$num);
        $pageCount = DI()->notorm->user_base->queryAll($sql, $params);
        $rs['pageCount'] = (int)$pageCount[0]['pageCount'];
        $rs['currentPage'] = $page;
        return $rs;
    }

    public function getPostDetail($postID,$page) {

        $num=9;
        $rs   = array();
        $sql = 'SELECT pb.id AS postID,pb.title,gb.name '
             . 'FROM post_detail pd,post_base pb ,group_base gb,user_base ub '
             . 'WHERE pb.id=pd.post_base_id AND pb.user_base_id=ub.id AND pb.group_base_id=gb.id AND pb.id=:post_id AND pd.floor=1' ;
        $params = array(':post_id' =>$postID );
        $rs['post'] = DI()->notorm->post_base->queryAll($sql, $params);

        $rs['reply']= DI()->notorm->post_detail
        ->SELECT('post_detail.text,user_base.nickName,post_detail.createTime')
        ->WHERE('post_base.id = ?',$postID)
        ->order('floor ASC')
        ->limit(($page-1)*$num,$num)
        ->fetchAll();

        $sql = 'SELECT ceil(count(*)/:num) AS pageCount '
         . 'FROM post_detail '
         . 'WHERE post_base_id=:post_id ';

        $params = array(':post_id' =>$postID,':num' =>$num);
        $pageCount = DI()->notorm->user_base->queryAll($sql, $params);
        $rs['pageCount'] = (int)$pageCount[0]['pageCount'];
        $rs['currentPage'] = $page;
        return $rs;
    }



    protected function getTableName($id) {
        return 'user';
    }
}
