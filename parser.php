<?php

$file = $argv[1] ?: "times.csv";

if (($handle = fopen($file, 'r')) !== false) 
{
	$daily_totals = [];
	$weekly_totals = [];
	$min_date = date('Y-m-d');
	$max_date = "1970-01-01";
	
	while($line = fgetcsv($handle)) 
	{
		if($line[0] == "Task Name")
		{
			continue;
		}
		
		array_walk($line, function (&$item) {
			str_replace('"', '', $item);
		});
		
		$task = $line[0];
		preg_match("/([0-9]*)h ([0-9]*)m/i", $line[1], $task_time);
		preg_match("/([0-9]*)h ([0-9]*)m/i", $line[2], $session_time);
		$date = date('Y-m-d', strtotime($line[3]));
		
		$min_date = $date < $min_date ? $date : $min_date;
		$max_date = $date > $max_date ? $date : $max_date;
		
		$weekly_totals[$task] = $task_time[1] + (float)($task_time[2] / 60.0);
		$daily_totals[$task][$date] += $session_time[1] + (float)($session_time[2] / 60.0);
	}
	
	ksort($daily_totals);
	ksort($weekly_totals);
	$pad_base = max(array_map('strlen', array_keys($weekly_totals)));
	
	printf("\n\nWeekly totals (%s - %s)\n\n", $min_date, $max_date);
	foreach($weekly_totals as $task => $time)
	{
		printf("%s%s\n", str_pad($task, ($pad_base + 5)), number_format($time, 1));
	}
	
	print "\n\nDaily totals\n\n";
	foreach($daily_totals as $task => $days)
	{
		ksort($days);
		printf("%s\n", $task);
		
		foreach($days as $day => $time)
		{
			printf("%s%s\n", str_pad($day, 15), number_format($time, 1));
		}
		
		print "\n";
	}
	
	print "\n";
} 
else 
{
	echo "File not found...\n";
}
