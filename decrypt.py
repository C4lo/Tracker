from data.en import load_ngrams
from score.ngram import NgramScorer
from score.ioc import IocScorer
from breaking.vigenere import KeylengthDetector
from breaking.vigenere import VigenereBreak

ciphertext, masker = Masker.from_text("HDSIOEYQOCAA")

s = IocScorer(alphabet_size=26)
KeylengthDetector(s).detect(ciphertext) #  prints candidate key lengths

key_length = 4 #  assume you want to try key length 4
scorer = NgramScorer(load_ngrams(1)) #  must be 1, because Caesar ciphers are interleved
breaker = VigenereBreak(key_length, scorer)
decryption, score, key = breaker.guess(ciphertext)[0]
print("Vigenere decryption (key={}, score={}):\n---\n{}---\n"
      .format(key, score, masker.extend(decryption)))