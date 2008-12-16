task :build do
  `staticmatic build ./`
end

task :deploy => :build do
  `scp -r site/* zencoding.net:~/web/public/`
end

task :preview do
  `staticmatic preview ./`
end