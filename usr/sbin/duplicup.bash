#!/bin/sh
target_url='file:///mnt/Backup/susesystem'
log_dest="/var/log/duplicity/duplog`date +"%Y-%m-%d"`"
err_dest="/home/jakubmede/Desktop/backup_in_progress"
export PASSPHRASE=$(ecret-tool lookup backup UbuBack)
rm /home/jakubmede/Desktop/backup_error
duplicity remove-older-than 9M --force "$target_url" >> "$log_dest" 2>> "$err_dest"
duplicity remove-all-inc-of-but-n-full 2 --force "$target_url" >> "$log_dest" 2>> "$err_dest"
duplicity -vi --include-filelist /mnt/Backup/backup_include --exclude '**' --volsize 666 --full-if-older-than 90D / "$target_url" > "$log_dest" 2> "$err_dest"
if [ -s $err_dest ]
then
	chmod 444 $err_dest
	chown jakubmede:user $err_dest
	mv -f $err_dest /home/jakubmede/Desktop/backup_error
else
	rm $err_dest
fi
