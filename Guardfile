guard :concat, :type => "css", :files => %w[cosmo font-awesome animate screen], :input_dir => "public/assets/css", :output => "public/assets/css/styles.min"

guard :concat, :type => "js", :files => %w[app], :input_dir => "public/assets/js", :output => "public/assets/js/scripts.min"

require 'cssmin'
require 'jsmin'

guard :refresher do
  watch('public/assets/css/styles.min.css') do |m|
    css = File.read(m[0])
    File.open(m[0], 'w') { |file| file.write(CSSMin.minify(css)) }
  end
  watch('public/assets/js/scripts.min.js') do |m|
    js = File.read(m[0])
    File.open(m[0], 'w') { |file| file.write(JSMin.minify(js)) }
  end
end