#!/usr/bin/perl
$|=1;
$server='10.1.0.1';
while (<>) {
    @X = split;
    $url = $X[0];
    if ($url =~ /^http:\/\/[^\/]*\/iavs4x\// && $url !~ m/$server/o) {
        $url =~ s/^http:\/\/[^\/]*\/iavs4x\//http:\/\/$server\/iavs4x\//o;
        print "$url\n";
    } else {
        print "\n";
    }
}
