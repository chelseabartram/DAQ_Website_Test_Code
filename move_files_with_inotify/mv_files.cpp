#include "inotify_process.h"

int main(int argc,char *argv[])
{
  inotify_process ROOT_hist_process;

  //Needed to initialize the minimum timestamp on existing files in the directory
  ROOT_hist_process.getAllFiles();

  while(1)
    {
      ROOT_hist_process.notify();
    }
  return 0;
}
