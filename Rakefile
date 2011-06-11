task :build do
  `nanoc compile`
end

task :deploy => :build do
  `scp -r output/* zencoding.net:~/web/public/`
end

task :preview => :build do
  `nanoc view`
end
