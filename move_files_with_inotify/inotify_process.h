#include <string>
#include <vector>
#include <list>


#ifndef INOTIFY_PROCESS_H
#define INOTIFY_PROCESS_H

class inotify_process
{
 public:
  inotify_process();
  void processHist(std::vector<std::string>, int);
  void processFiles(std::string, int);
  void notify();
  int getTimestamp(std::string);
  void addToVector(std::string);
  int getAllFiles();


 private:

  /* Size of histogram vector when its contents will be moved to another directory */
  int max_size;

  /* Minimum values for timestamps. Initialized in int main(). Should initialize in constructor probably.*/

  int drpc_min;
  int fepc1_min;
  int fepc2_min;
  int fepc3_min;
  int fepc4_min;

  /* Timestamps which should match for a single histogram vector */    

  int dr_hist_timestamp;
  int fe1_hist_timestamp;
  int fe2_hist_timestamp;
  int fe3_hist_timestamp;
  int fe4_hist_timestamp;

  /* Define strings for beginnings of file names to be matched */

  std::string drpc;
  std::string fepc1;
  std::string fepc2;
  std::string fepc3;
  std::string fepc4;

  /* Histogram vectors to be filled with file names that meet criteria for lowest timestamp and correct naming scheme */
  /* Size will never exceed 6. Maybe I should initialize the size */

  std::vector<std::string> DR_DRAssembler_START;
  std::vector<std::string> FE1_RATAssembler_START;
  std::vector<std::string> FE2_RATAssembler_START;
  std::vector<std::string> FE3_RATAssembler_START;
  std::vector<std::string> FE4_RATAssembler_START;

  /* Linked lists to sort files based on timestamp */

  std::list<int> drpc_files;
  std::list<int> fepc1_files;
  std::list<int> fepc2_files;
  std::list<int> fepc3_files;
  std::list<int> fepc4_files;
};

#endif
