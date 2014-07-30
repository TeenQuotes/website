if File.exists?("./config.rb")
	# Compile on start.
	puts `compass compile --time --quiet`
	guard :compass do
	  watch(%r{(.*)\.s[ac]ss$})
	end
end
guard :concat, :type => "css", :files => %w[cosmo animate font-awesome screen], :input_dir => "public/assets/css", :output => "public/assets/css/styles.min"

guard :concat, :type => "js", :files => %w[jquery-2.1.0.min wow.min bootstrap.min mailgun-validator app], :input_dir => "app/assets/js", :output => "public/assets/js/scripts.min"

require 'cssmin'
require 'jsmin'

module ::Guard
  class Refresher < Guard
  end
end

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