#!/usr/bin/perl
use strict;
use Data::Dumper; 
use Text::CSV;

my $csv = Text::CSV->new();
 
my $file = $ARGV[0] or die "Need to get CSV file on the command line\n";
 
my $sum = 0;
open(my $data, '<', $file) or die "Could not open '$file' $!\n";

# Skip header line
<$data>;

while (my $line = <$data>) {
  chomp $line;
 
  if ($csv->parse($line)) {
    my @c = $csv->fields();
    my $email =$c[2];
    my $phone = $c[23]; 
    my $mobile = $c[25];
      
    if ($email ne "" or $phone ne "" or $mobile ne "") {
      # We have at least one contact 
      
      my $first = $c[3];
      my $last = $c[4];
      
      if ($first eq "" and $last eq "") {
        # No first/last, split from name
        my @parts = split(/ /, $c[0]);
        $first = shift(@parts);
        $last = join(' ', @parts);
      }

      my $address1 = $c[6];
      my $address2 = $c[7];

      # Escape quotes in names & address
      $first =~ s/'/''/g;
      $last =~ s/'/''/g;
      $address1 =~ s/'/''/g;
      $address2 =~ s/'/''/g;
      
      # If both phone and mobile are empty, insert null for mobile
      # otherwise if mobile is empty, use phone
      if ($mobile eq "") {
        if ($phone ne "") {
          $mobile = $phone;
        }
      }
      $mobile =~ s/\D//g;
      if ($mobile eq "") {
        $mobile = 'null';
      } else {
        $mobile = "'$mobile'";
      }
      
      # Quote email or use null
      if ($email eq "") {
        $email = 'null';
      } else {
        $email =~ s/'/''/g;
        $email = "'$email'";
      }

      printf(
        "insert into `de_customers` (`email`, `mobile`, `address1`, `address2`, `postcode`, `state_id`, `country_id`, `title`, `first_name`, `last_name`, `created_date`, `created_by`) " .
        "values (%s, %s, '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', now(), 1);\n",
        $email, $mobile, $address1, $address2, $c[12], '1', 'Australia', '', $first, $last);
    } else {
        warn "No contact info: $line\n";
    }
  } else {
      warn "Line could not be parsed: $line\n";
  }
}
