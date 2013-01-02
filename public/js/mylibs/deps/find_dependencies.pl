#!/usr/bin/perl

use strict;

require "./modules.pl";
require "./deps_on_base.pl";

use Data::Dumper;

my %deps = ();
for(@main::modules){
    $deps{$_} = ();
}
for(@main::deps_on_base){
    $deps{$_}{'base'}++;
}

`rm ../new_hotness/*`;#tear down so we can build up

#first pass
my $dir_name = "../old_scripts";
my $dh;
opendir($dh, $dir_name) || die;
while (my $fname = readdir $dh){
	my $full_file = "$dir_name/$fname";
	#valid full file
	if($full_file =~ /\.js$/ && $full_file !~ /^\.\.?$/ && -f $full_file && !-d $full_file && !-z $full_file){
		my $dname = $fname;#to be the new file name
        $dname =~ s/\.js$//;
        if(open(my $ff, '<', $full_file)){
            while(<$ff>){
                my $line = $_;
                if(/new\s*_namespace.(\w+)\b/){
                #if(/_globs.(\w+)\b/){

                    my $id = $1;
                    #$id = 'main' if (grep(/^$id$/, @main::modules));#no module then it's coming directly from main

                    print STDERR "ERROR: you are an idiot ($id): $line" if ($dname =~ /$id/i);

                    $deps{$dname}{$id}++
                }
            }
        }
	}
}
closedir($dh);

print Dumper \%deps;

{
local $/ = undef;#slurp mode
#second pass
opendir($dh, $dir_name) || die;
while (my $fname = readdir $dh){
	my $full_file = "$dir_name/$fname";
	#valid full file
	if($full_file =~ /\.js$/ && $full_file !~ /^\.\.?$/ && -f $full_file && !-d $full_file && !-z $full_file){

		my $dname = lc($fname);#to be the new file name
        $dname =~ s/\.js$//;

	    my $full_out_file = "../new_hotness/$fname";

print "OUT: $full_out_file\n";

        if(open(my $ff, '<', $full_file)){
            my $slurp = <$ff>;

            #replacements
            $slurp =~ s/(clone|cutDown|openDot|roundRect|roundTab)/BASE.$^N/g if($fname ne "base.js");
            $slurp =~ s/new\s*_namespace\.(\w*)\b/"new ".(uc($1))/eg;
            $slurp =~ s/_namespace\..*?\s*=\s*function\s*\(\s*\w*\s*\)\s*{//;
            $slurp =~ s/\$\(\s*function\(\s*\)\s*\{\n//s;
            $slurp =~ s/\}\;\s*\}\)\;\s$/\n/s;
            $slurp =~ s/var _namespace = namespace\("DBFlyer"\)\;\n//gs;
            if($dname eq 'base'){
                $slurp =~ s/function\s*(\w+)\s*\(/var ret = {};\n$&/s;
                $slurp =~ s/function\s*(\w+)\s*\(/ret.$1 = function(/sg;
            }

            $slurp =~ s(_namespace\.(\w+)\b)(grep(/^$1$/, @main::modules) ? uc($1) : 'FAIL')exsg;

            my @deps = keys %{$deps{$dname}};
            my @upper_deps = keys %{$deps{$dname}};
            grep {$_ = lc("'$_'");} @deps;
            grep {$_ = uc($_);} @upper_deps;
            my $dep_statement = '';
            if(@deps > 0){
                $dep_statement = "[".(join(",",@deps))."],";
            }
            my $dep_vars = join(",",@upper_deps);

            $slurp = qq!
define(${dep_statement}function($dep_vars){
!.$slurp;

            if ($dname eq 'base'){
                $slurp .= "return \$ret\n";
            }
            $slurp .= qq!\n});!;

            #print $slurp;
            if(open(my $out_ff, '>', $full_out_file)){
                print $out_ff $slurp;
            }
        }
	}
}
closedir($dh);
}


