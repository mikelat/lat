<?php
/*********************************************************************
 * Highlighter class - highlights SQL with preg and some compromises
* @Author dzver <dzver@abv.bg>
* @Copyright GNU v 3.0
*********************************************************************/

class Highlighter {
	/*
	 protected $colors - key order is important because of highlighting < and >
	chars and not encoding them to &lt; and &gt;
	*/
	protected static $colors = Array('chars' => 'grey', 'keywords' => 'blue', 'joins' => 'gray', 'functions' => 'violet', 'constants' => 'red');
	/*
	 lists are not complete.
	*/
	protected static $words = Array (
			'keywords' =>
			array('SELECT', 'UPDATE', 'INSERT', 'DELETE', 'REPLACE', 'INTO', 'CREATE', 'ALTER', 'TABLE', 'DROP', 'TRUNCATE', 'FROM',
					'ADD', 'CHANGE', 'COLUMN', 'KEY',
					'WHERE', 'ON', 'CASE', 'WHEN', 'THEN', 'END', 'ELSE', 'AS',
					'USING', 'USE', 'INDEX', 'CONSTRAINT', 'REFERENCES', 'DUPLICATE',
					'LIMIT', 'OFFSET', 'SET', 'SHOW', 'STATUS',
					'BETWEEN', 'AND', 'IS', 'NOT', 'OR', 'XOR', 'INTERVAL', 'TOP',
					'GROUP BY', 'ORDER BY', 'DESC', 'ASC', 'COLLATE', 'NAMES', 'UTF8', 'DISTINCT', 'DATABASE',
					'CALC_FOUND_ROWS', 'SQL_NO_CACHE', 'MATCH', 'AGAINST', 'LIKE', 'REGEXP', 'RLIKE',
					'PRIMARY', 'AUTO_INCREMENT', 'DEFAULT', 'IDENTITY', 'VALUES', 'PROCEDURE', 'FUNCTION',
					'TRAN', 'TRANSACTION', 'COMMIT', 'ROLLBACK', 'SAVEPOINT', 'TRIGGER', 'CASCADE',
					'DECLARE', 'CURSOR', 'FOR', 'DEALLOCATE'
			),
			'joins' => array('JOIN', 'INNER', 'OUTER', 'FULL', 'NATURAL', 'LEFT', 'RIGHT'),
			'chars' => '/([\\.,\\(\\)<>:=`]+)/i',
			'functions' => array(
					'MIN', 'MAX', 'SUM', 'COUNT', 'AVG', 'CAST', 'COALESCE', 'CHAR_LENGTH', 'LENGTH', 'SUBSTRING',
					'DAY', 'MONTH', 'YEAR', 'DATE_FORMAT', 'CRC32', 'CURDATE', 'SYSDATE', 'NOW', 'GETDATE',
					'FROM_UNIXTIME', 'FROM_DAYS', 'TO_DAYS', 'HOUR', 'IFNULL', 'ISNULL', 'NVL', 'NVL2',
					'INET_ATON', 'INET_NTOA', 'INSTR', 'FOUND_ROWS',
					'LAST_INSERT_ID', 'LCASE', 'LOWER', 'UCASE', 'UPPER',
					'LPAD','RPAD','RTRIM','LTRIM',
					'MD5','MINUTE', 'ROUND',
					'SECOND', 'SHA1', 'STDDEV', 'STR_TO_DATE', 'WEEK'),
			'constants' => '/(\'[^\']*\'|[0-9]+)/i'
	);

	public static function sql($sql)
	{
		$sql = str_replace('\\\'', '\\&#039;', $sql);
		foreach(static::$colors as $key=>$color)
		{
			if (in_array($key, Array('constants', 'chars'))) {
				$regexp = static::$words[$key];
			}
			else {
				$regexp = '/\\b(' . join("|", static::$words[$key]) . ')\\b/i';
			}
			$sql = preg_replace($regexp, '<span style="color:'.$color."\">$1</span>", $sql);
		}
		return $sql;
	}
}
?>
<div class="tc">
<strong>Coded By:</strong> Mike Lat
<strong class="pl">Version:</strong> <?php echo $version; ?>
<strong class="pl">Queries Executed:</strong> <?php echo $queries; ?>
<strong class="pl">Query Time:</strong> <?php echo $query_time; ?>
<strong class="pl">Exec Time:</strong> <?php echo $exec_time; ?>
<strong class="pl"><a href="<?php echo Url::make('forum/cache') ?>">force cache reload</a></strong>
</div>
<div id="debug">
	<a href="#" onclick="$('#debug_data').toggle(); return false;">debug</a>
</div>
<ul id="debug_data">
	<li id="debug_head">
		Debug Summary
		<a href="#" onclick="$('#debug_data').toggle(); return false;">close debug summary</a>
		<a href="#" onclick="$('.debug-debug').toggleClass('on'); return false;" class="debug-debug">toggle debug info</a>
	</li>
	<?php foreach ($log as $l) {
		echo '<li class="debug-' . $l[0] . '"><strong>[' . strtoupper($l[0]) . ']</strong> '
	 	.	($l[0] == 'query' ? Highlighter::sql($l[1]) : $l[1])
		.	($l[2] > 0 ? ' <em>(executed in '.number_format($l[2], 6). 's)</em></li>' : '</li>');
	} ?>

</ul>