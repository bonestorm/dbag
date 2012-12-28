#!/usr/bin/perl

use strict;

my $dir_name = "./";
my $dead_prefix = "DBFlyer_";#prefix to kill
my $dh;
opendir($dh, $dir_name) || die;
while (my $fname = readdir $dh){
	my $full_file = "$dir_name/$fname";
	#valid full file
	if($full_file !~ /^\.\.?$/ && -f $full_file && !-d $full_file && !-z $full_file){
		my $nname = $fname;#to be the new file name
		if($nname =~ s/^$dead_prefix//){
			my $new_full_file = "${dir_name}mylibs/$nname";
			print qq!mv "$full_file" "$new_full_file"\n!;
			`mv "$full_file" "$new_full_file"`;
		}
	}
}
closedir($dh);

