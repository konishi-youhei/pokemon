<?php

ini_set('log_errors', 'on');
ini_set('error_log', 'php.log');
session_start();

$monsters = array();

// 履歴管理クラス
// 性別クラス
// 抽象くらす(生き物クラス)
// 人クラス
// モンスタークラス
// 魔法攻撃モンスタークラス
// 毒攻撃モンスタークラス

// メソッド作る
// POST送信されていた場合

// 履歴管理クラス
class History{
  public static function set($str){
    if(empty($_SESSION['history'])) $_SESSION['history'] = '';
    $_SESSION['history'] .= $str.'<br>';
  }
  public static function clear(){
    unset($_SESSION['history']);
  }
}

// 性別クラス
class Sex{
  const MAN = 1;
  const WOMAN = 2;
  const OKAMA = 3;
}
// 抽象クラス
abstract class Creature{
  protected $name;
  protected $hp;
  protected $attackMin;
  protected $attackMax;
  abstract public function sayCry();
  public function setName($str){
    $this->name = $str;
  }
  public function getName(){
    return $this->name;
  }
  public function setHp($num){
    $this->hp = $num;
  }
  public function getHp(){
    return $this->hp;
  }
  public function attack($targetObj){
    $attackPoint = mt_rand($this->attackMin, $this->attackMax);
    if(!mt_rand(0, 9)){
      $attackPoint = $attackPoint * 1.5;
      $attackPoint = (int)$attackPoint;
      History::set($this->getName().'のクリティカルヒットだー！');
    }
    $targetObj->setHp($targetObj->getHp()-$attackPoint);
    History::set($attackPoint.'ポイントのダメージを与えた！');
  }
}
// 人クラス
class Human extends Creature{
  protected $sex;
  public function __construct($name, $sex, $hp, $attackMin, $attackMax){
    $this->name = $name;
    $this->sex = $sex;
    $this->hp = $hp;
    $this->attackMin = $attackMin;
    $this->attackMax = $attackMax;
  }
  public function getSex(){
    return $this->sex;
  }
  public function setSex($num){
    $this->sex = $num;
  }
  public function sayCry(){
    History::set($this->name.'が叫ぶ!');
    switch($this->sex){
      case Sex::MAN :
        History::set('ぐはぁっ！');
        break;
      case Sex::WOMAN :
        History::set('いやん！');
        break;
      case Sex::OKAMA :
        History::set('あーん♡');
        break;
    }
  }
}
// モンスタークラス
class Monster extends Creature{
  protected $img;
  public function __construct($name, $hp, $img, $attackMin, $attackMax){
    $this->name = $name;
    $this->hp = $hp;
    $this->img = $img;
    $this->attackMin = $attackMin;
    $this->attackMax = $attackMax;
  }
  public function getImg(){
    return $this->img;
  }
  public function sayCry(){
    History::set($this->name.'が叫ぶ！');
    History::set('ぐわっーーーー！');
  }
  public function attack($targetObj){
    $attackPoint = mt_rand($this->attackMin, $this->attackMax);
    if(!mt_rand(0, 9)){
      $attackPoint = $attackPoint * 1.5;
      $attackPoint = (int)$attackPoint;
      History::set($this->getName().'のクリティカルヒットだー！');
    }
    $targetObj->setHp($targetObj->getHp()-$attackPoint);
    History::set($attackPoint.'ポイントのダメージを受けた！');
  }
}

// 魔法攻撃モンスタークラス
class MagicMonster extends Monster{
  private $magicAttack;
  public function __construct($name, $hp, $img, $attackMin, $attackMax, $magicAttack){
    $this->magicAttack = $magicAttack;
    parent::__construct($name, $hp, $img, $attackMin, $attackMax);
  }
  public function getMagicAttack(){
    return $this->magicAttack;
  }
  public function attack($targetObj){
    if(!mt_rand(0, 4)){
      History::set($this->name.'の魔法攻撃');
      $targetObj->setHp($targetObj->getHp() - $this->magicAttack);
      History::set($this->magicAttack.'ポイントのダメージを受けた！');
    }else{
      parent::attack($targetObj);
    }
  }
}

// 回復モンスタークラス
class PoisonMonster extends Monster{
  private $poisonAttack;
  private $recovary;
  public function __construct($name, $hp, $img, $attackMin, $attackMax, $poisonAttack, $recovary){
    $this->poisonAttack = $poisonAttack;
    $this->recovary = $recovary;
    parent::__construct($name, $hp, $img, $attackMin, $attackMax);
  }
  public function getPoisonAttack(){
    return $this->poisonAttack;
  }
  public function getRecovary(){
    return $this->recovary;
  }
  public function attack($targetObj){
    $num = mt_rand(0, 4);
    if($num < 3){
      History::set($this->name.'の毒攻撃');
      $targetObj->setHp($targetObj->getHp() - $this->poisonAttack);
      History::set($this->poisonAttack.'ポイントのダメージを受けた！');
    }elseif(1 <= $num && $num < 4) {
      History::set($this->name.'が特殊な毒で回復した');
      $this->hp = $this->hp + $this->recovary;
      History::set($this->recovary.'ポイント回復した');
      if($this->getHp() > $_SESSION['monsterHp']){
        $this->setHp($_SESSION['monsterHp']);
      }
    }else{
      parent::attack($targetObj);
    }
  }
}

$human = new Human('勇者', Sex::OKAMA, 150, 40, 120);
$monsters[] = new Monster( 'ドラゴネア', 100, 'img/モンスター１.jpg', 20, 40 );
$monsters[] = new MagicMonster( 'オーマンダ', 300, 'img/モンスター２.jpg', 20, 60, mt_rand(50, 100) );
$monsters[] = new Monster( 'ドラキュー', 200, 'img/モンスター３.jpg', 30, 50 );
$monsters[] = new MagicMonster( 'ザリシャー', 400, 'img/モンスター４.jpeg', 50, 80, mt_rand(60, 120) );
$monsters[] = new Monster( 'ダイスラー', 150, 'img/モンスター５.png', 30, 60 );
$monsters[] = new Monster( 'バリフリー', 100, 'img/モンスター６.jpeg', 10, 30 );
$monsters[] = new PoisonMonster( 'シリシャー', 120, 'img/モンスター７.png', 20, 30, 60, 30 );
$monsters[] = new PoisonMonster( 'ワルフリー', 180, 'img/モンスター８.jpg', 30, 50, 60, 40 );

function createMonster(){
  global $monsters;
  $monster = $monsters[mt_rand(0, 7)];
  History::set($monster->getName().'が現れた!');
  $_SESSION['monster'] = $monster;
  $_SESSION['monsterHp'] = $monster->getHp();
}
function createHuman(){
  global $human;
  $_SESSION['human'] =  $human;
  $_SESSION['humanHp'] =  $human->getHp();
}
function init(){
  History::clear();
  History::set('ゲームスタート！');
  $_SESSION['knockDownCount'] = 0;
  $_SESSION['gameOver'] = 0;
  createHuman();
  createMonster();
}
function gameOver(){
  $_SESSION['gameOver'] = 1;
}
// $_SESSION['history']の文字列の削除開始場所を見つける関数
function strpos2($str, $needle, $n = 0) {
  $offset = 0;
  $len = strlen($needle);
  while ($n-- >= 0 && ($pos = strpos($str, $needle, $offset)) !== false) {
    $offset = $pos + $len;
  }
  return $pos;
}

// ポスト送信されていた場合
if(!empty($_POST)){
  $attackFlg = (!empty($_POST['attack'])) ? true : false;
  $startFlg = (!empty($_POST['start'])) ? true : false;
  $recovaryFlg = (!empty($_POST['recovary'])) ? true : false;
  $clearFlg = (!empty($_POST['clear'])) ? true : false;
  $restartFlg = (!empty($_POST['restart'])) ? true : false;
  error_log('POSTされた！');

  if($startFlg){
    History::set('ゲームスタート');
    init();
  }elseif($clearFlg){
    if(mb_substr_count($_SESSION['history'], "<br>") > 2){
      $pos = strpos2($_SESSION['history'], '<br>', 1);
      $_SESSION['history'] = substr($_SESSION['history'], $pos + 4, strlen($_SESSION['history']));
    }else{
      $_SESSION['history'] = array();
    }
  }elseif($restartFlg){
    $_SESSION = array();
  }else{
    if($attackFlg){
      // モンスターに攻撃を与える
      History::set($_SESSION['human']->getName().'の攻撃！');
      $_SESSION['human']->attack($_SESSION['monster']);
      // $_SESSION['monster']->sayCry();

      // モンスターが攻撃をする
      History::set($_SESSION['monster']->getName().'の攻撃を受けた');
      $_SESSION['monster']->attack($_SESSION['human']);
      // $_SESSION['human']->sayCry();

      // 自分のHPが0以下になったらゲームオーバー
      if($_SESSION['human']->getHp() <= 0){
        gameOver();
      }else{
        // モンスターのHPが0になったら別のモンスター出現
        if($_SESSION['monster']->getHp() <= 0){
          History::set($_SESSION['monster']->getName().'を倒した');
          createMonster();
          $_SESSION['knockDownCount'] = $_SESSION['knockDownCount'] + 1;
        }
      }
    }elseif($recovaryFlg){
      if(!isset($_SESSION['num'])) $_SESSION['num'] = 3;
      if($_SESSION['num'] <= 0){
        History::set('もうポーションが切れてしまった、、、');
      }elseif($_SESSION['human']->getHp() == $_SESSION['humanHp']){
        History::set('体力は満タンです！');
      }else{
        $_SESSION['human']->setHp($_SESSION['human']->getHp() + mt_rand(50, 150));
        $_SESSION['num'] = $_SESSION['num'] - 1;
        History::set($_SESSION['human']->getName().'は回復した。');
        History::set('ポーションの残りは'.$_SESSION['num'].'個になった。');
        if($_SESSION['human']->getHp() > $_SESSION['humanHp']){
          $_SESSION['human']->setHp($_SESSION['humanHp']);
        }
      }
    }else{
      History::set('逃げた。');
      createMonster();
    }
  }
  $_POST = array();
}
?>

<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <title>アウトプットゲーム</title>
    <link rel="stylesheet" href="style.css">
  </head>
  <body onload="printMoji()">
    <div class="container">
      <?php if(empty($_SESSION)){ ?>
        <div class="start">
          <form action="index.php" method="post">
            <h2>GAME START ?</h2>
            <input type="submit" name="start" value="ゲームリスタート">
          </form>
        </div>
      <?php }elseif($_SESSION['gameOver'] == 1){ ?>
        <div class="gameOver">
          <form action="index.php" method="post">
            <h2>GAME OVER</h2>
            <input type="submit" name="restart" value="トップに戻る">
            <p>モンスター撃破数は <span><?php echo $_SESSION['knockDownCount'] ?></span> 体でした。</p>
          </form>
        </div>
      <?php }else{ ?>
      <div class="monster">
        <div class="contents-left">
          <p><?php echo $_SESSION['monster']->getName(); ?> 　<span><?php echo $_SESSION['monster']->getHp(); ?> / <?php echo $_SESSION['monsterHp'] ?></span></p>
          <p>HP：<meter min="0" max="<?php echo $_SESSION['monsterHp'] ?>" value="<?php echo $_SESSION['monster']->getHp(); ?>"></meter></p>
          <div class="hp"></div>
        </div>
        <div class="monster-img">
          <img src="<?php echo $_SESSION['monster']->getImg(); ?>" alt="">
        </div>
      </div>
      <div class="human">
        <div class="human-img">
          <img src="img/勇者.jpg" alt="">
        </div>
        <div class="contents-right">
          <p>勇者　<span><?php echo $_SESSION['human']->getHp(); ?> / <?php echo $_SESSION['humanHp'] ?></span></p>
          <p>HP：<meter min="0" max="<?php echo $_SESSION['humanHp'] ?>" value="<?php echo $_SESSION['human']->getHp(); ?>"></meter></p>
          <div class="hp"></div>
        </div>
      </div>
      <div class="box">
        <?php if(empty($_SESSION['history'])){ ?>
          <form class="actionBtn" action="index.php" method="post" class="js-form-action">
            <input type="submit" name="attack" value="▷攻撃する">
            <input type="submit" name="recovary" value="▷回復する"><br>
            <input type="submit" name="restart" value="▷リスタート">
            <input type="submit" name="escape" value="▷逃げる">
          </form>
        <?php }else{ ?>
          <p id="text"></p>
          <form class="clearBtn" action="index.php" method="post">
            <input type="submit" name="clear" value="▼" style="border: none;">
          </form>
        <?php } ?>
      </div>
    <?php } ?>
    </div>
  </body>
  <script>
    var i = 0;
    var pointString;
    var moji="<?php echo $_SESSION['history']; ?>";
    function printMoji(){
      document.getElementById("text").innerHTML　=　moji.substring(0,i++);
      if(i<=moji.length) {
       setTimeout("printMoji()",50);
      }
    }
  </script>
</html>
