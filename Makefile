LINGUIST_PATH = linguist


$(LINGUIST_PATH):
	git clone https://github.com/github/linguist.git

clean:
	rm -rf $(LINGUIST_PATH)


code-generate: $(LINGUIST_PATH)
	mkdir -p src/Babelfish/Data
	scripts/dump-data