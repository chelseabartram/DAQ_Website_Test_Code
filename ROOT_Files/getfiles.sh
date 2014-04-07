#!/bin/bash
cd /home/mcalpha/ROOT_Files/

ls -1 | grep "DRPC_MM_DR" > drpc_files.dat
ls -1 | grep "FEPC1_MM_FE" > fepc1_files.dat
ls -1 | grep "FEPC2_MM_FE" > fepc2_files.dat
ls -1 | grep "FEPC3_MM_FE" > fepc3_files.dat
ls -1 | grep "FEPC4_MM_FE" > fepc4_files.dat